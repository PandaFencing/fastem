<?php
namespace Application\Common;

use Zend\Mvc\Controller\AbstractActionController;

class BaseController extends AbstractActionController
{
    public $dbs = [];
    public $tables = [];
    public $logger;
    /**
     * @param string $db
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getDB($db = 'db')
    {
        if (!array_key_exists($db, $this->dbs)) {
            try {
                $this->dbs[$db] = $this->getServiceLocator()->get($db);
            } catch (\Exception $e) {
                $this->getLogger()->error($db . ' not found');
            }
        }
        return $this->dbs[$db];
    }

    /**
     * @param $n
     * @param string $db
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTable($n, $db = 'db')
    {
        if (false === array_key_exists($n, $this->tables)) {
            $this->tables[$n] = new SafeTableGateway($n, $this->getDB($db));
        }
        return $this->tables[$n];
    }

    /**
     * Log error message
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            try {
                $this->logger = $this->getServiceLocator()->get('logger');
            } catch (\Exception $e) {
                error_log(__LINE__.':'.__FUNCTION__.':'.__CLASS__.': Logger not found');
            }
        }
        return $this->logger;
    }
    public function isValidJson($str)
    {
        $pcre_regex = '
  /
  (?(DEFINE)
     (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )
     (?<boolean>   true | false | null )
     (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
     (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
     (?<pair>      \s* (?&string) \s* : (?&json)  )
     (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
     (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
  )
  \A (?&json) \Z
  /six
';
        return (bool) preg_match($pcre_regex, $str);
    }
    public function filterArray(array $a)
    {
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $v = $this->filterArray($v);
            } elseif (is_string($v)) {
                if (true === $this->isValidJson($v)) {
                    $tmp_arr = json_decode($v, true);
                    if (is_array($tmp_arr)) {
                        $tmp_arr = $this->filterArray($tmp_arr);
                        $v = JsonConverter::toJson($tmp_arr);
                    }
                } else {
                    $v = StringHtmlTidy::allclean($v);
                }
            } elseif (is_int($v)) {
                $v = (int) $v;
            } elseif (is_float($v)) {
                $v = (float) $v;
            } elseif ($v instanceof \DateTime) {
                $timestamp = strtotime($v);
                if (false !== $timestamp) {
                    $v->setTimestamp(strtotime($timestamp));
                }
                $v = $v->format('Y-m-d H:i:s');
            }
            $a[$k] = $v;
        }
        return $a;
    }
    /**
     * Filter post data
     * @param array $def_idx key define index
     * @param array $params post data
     * @param boolean $autofill may auto fill
     * @return array
     */
    public function filterPostData($def_idx, $params, $autofill = true)
    {
        $new_params = [];
        foreach ($def_idx as $k => $v) {
            if (true === array_key_exists($k, $params)) {
                if (is_array($v)) {
                    $new_params[$k] = JsonConverter::toJson($this->filterArray($params[$k])); //Array to json
                } elseif (is_string($v)) {
                    if (is_array($params[$k])) {
                        $new_params[$k] = JsonConverter::toJson($this->filterArray($params[$k])); // Array to json also
                    } else {
                        $tmp_v = (string) $params[$k];
                        if (true === $this->isValidJson($tmp_v)) {
                            $tmp_arr = json_decode($tmp_v, true);
                            if (is_array($tmp_arr)) {
                                $new_params[$k] = JsonConverter::toJson($this->filterArray($tmp_arr));
                            } else {
                                $new_params[$k] = (null === $tmp_arr) ? '' : $tmp_v; //Not array
                            }
                        } else {
                            $new_params[$k] = StringHtmlTidy::allclean($tmp_v);
                        }
                    }
                } elseif (is_int($v)) {
                    $new_params[$k] = (int) $params[$k];
                } elseif (is_float($v)) {
                    $new_params[$k] = (float) $params[$k];
                } elseif ($v instanceof \DateTime) {
                    $tmp_time = $params[$k];
                    if ('' === $tmp_time) {
                        $timestamp = false;
                    } elseif (is_numeric($tmp_time) && mb_strlen($tmp_time) > 10) {
                        $timestamp = $tmp_time;
                    } else {
                        $timestamp = strtotime($tmp_time);
                    }
                    if (false !== $timestamp) {
                        $v->setTimestamp($timestamp);
                    }
                    $new_params[$k] = $v->format('Y-m-d H:i:s');
                }
            } else {
                if ($autofill === true) {
                    if ($v instanceof \DateTime) {
                        $new_params[$k] = $v->format('Y-m-d H:i:s');
                    } else {
                        $new_params[$k] = $v;
                    }
                }
            }
        }
        return $new_params;
    }
}