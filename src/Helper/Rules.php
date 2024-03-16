<?phpnamespace KooijmanInc\Suzie\Helper;class Rules{    public static function process(array $rules)    {//dump($rules);//        if (isset($rules[0]) && is_string($rules[0]) || (count($rules) === 1 && !ctype_digit((string)implode('', array_keys($rules))))) {dump('thisone?');//            $rules = [$rules];//        }        $allRules = [];        foreach ($rules as $key => $rule) {            if (is_string($rule) && in_array($rule, ['OR', 'AND'])) {                $allRules[] = $rule;            } elseif (isset($rule['field']) && isset($rule['operator']) && (isset($rule['value']) || $rule['value'] === null)) {                dump('check rules second: ', $key, $rule);            } elseif (!is_array($rule) || (!ctype_digit((string)$key) && is_array($rule))) {                $allRules[] = ['field' => $key, 'operator' => '=', 'value' => $rule];            } else {                dump("rule still to do", $key, $rule);            }        }        return $allRules;    }    public static function processToWhereAndBind(array $rules, $type = 'AND', $subBinds = 0)    {        $bind = [];        $queryParts = [];        if (in_array('OR', $rules)) {            $type = 'OR';        } elseif (in_array('AND', $rules)) {            $type = 'AND';        }        foreach ($rules as $rule) {            if (is_string($rule) && in_array($rule, ['OR', 'AND'])) {                continue;            }            if (is_string($rule)) {                dump('create where first ', $rule);                $queryParts[] = $rule;            } elseif (is_array($rule) && ctype_digit((string)implode('', array_keys($rule)))) {                dump('create where second ', $rule);                list($subWhere, $subBind) = self::processToWhereAndBind($rule, ($type === 'AND' ? 'OR' : 'AND'), $subBinds++);                $queryParts[] = "({$subWhere})";                $bind = array_merge($bind, $subBind);            } elseif (in_array($rule['operator'], ['!=', '<>'])) {                dump('create where third ', $rule);            } elseif (in_array($rule['operator'], ['=', '=='])) {                //dump('create where fourth ', $rule);                if (is_array($rule['value']) && count($rule['value']) === 0) {                    dump('fourth first ', $rule);                } elseif (is_array($rule['value'])) {                    dump('fourth second ', $rule);                } elseif ($rule['value'] === null) {                    dump('fourth third ', $rule);                } elseif ($rule['value'] === true) {                    dump('fourth fourth ', $rule);                } elseif ($rule['value'] === false) {                    dump('fourth fourth ', $rule);                } else {                    $bind[] = $rule['value'];                    $queryParts[] = "`{$rule['field']}` {$rule['operator']} ?";                }            } elseif ($rule['operator'] == '<') {                dump('create where fifth ', $rule);            } elseif ($rule['operator'] == '<=') {                dump('create where fifth ', $rule);            } elseif ($rule['operator'] == '>') {                dump('create where fifth ', $rule);            } elseif ($rule['operator'] == '>=') {                dump('create where fifth ', $rule);            } elseif ($rule['operator'] == 'LIKE') {                dump('create where fifth ', $rule);            }        }        $where = implode(" $type ", $queryParts);        return [$where, $bind];    }//    protected static function addToBind(array &$bind, int $subBinds, string $field, $value): string//    {////    }}