<?phpnamespace KooijmanInc\Suzie\FormBuilder\FormParts\Form;use KooijmanInc\Suzie\Exception\NotSupported;/** * Class AbstractForm * @property string $id */abstract class AbstractForm implements FormInterface{    /**     * @var string     */    protected string $id;    /**     * @var string     */    protected string $uuid;    /**     * @var string     */    protected string $action = '/';    /**     * @var string     */    protected string $method = 'post';    /**     * @var string|null     */    protected ?string $encType = null;    /**     * @var string|null     */    protected ?string $target = null;    /**     * @var string|null     */    protected ?string $autoComplete = null;    /**     * @var string|null     */    protected ?string $acceptCharset = null;    /**     * @var string|null     */    protected ?string $name = null;    /**     * @var string|null     */    protected ?string $rel = null;    /**     * @var bool|null     */    protected ?bool $novalidate = null;    /**     * @var array     */    protected array $showElements = [];    /**     * @var string|null     */    protected ?string $elementSize = null;    /**     * @var bool     */    protected bool $standardForm = true;    /**     * @var bool     */    protected bool $showAllLabels = true;    /**     * @var bool     */    protected bool $showAllPlaceholders = true;    /**     * @var bool     */    protected bool $showAllValues = true;    /**     * @var int     */    protected int $labelColWidth = 3;    /**     * @var array     */    private array $methodAllowed = ['get', 'post', 'put', 'delete'];    /**     * @var array     */    private array $enctypeAllowed = ['application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'];    /**     * @var array     */    private array $targetAllowed = ['_blank', '_self', '_parent', '_top', 'framename'];    /**     * @var array     */    private array $autoCompleteAllowed = ['off', 'on'];    /**     * @var array     */    private array $acceptCharsetAllowed = ['utf-8', 'utf-16', 'windows-1252', 'iso-8859-1'];    /**     * @var array     */    private array $novalidateAllowed = [true, false];    /**     * @var array     */    private array $relAllowed = ['external', 'help', 'license', 'next', 'nofollow', 'noopener', 'noreferrer', 'opener', 'prev', 'search'];    /**     * @var array     */    private array $elementSizeAllowed = [null, 'sm', 'lg'];    /**     * @param string $id     */    public function __construct(string $id)    {        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);        $this->id = $id;    }    /**     * @return string     */    public function getAction(): string    {        return $this->action;    }    public function setAction(string $action)    {        $this->action = $action;        return $this;    }    public function getMethod(): string    {        return $this->method;    }    public function setMethod(string $method)    {        if (in_array($method, $this->methodAllowed)) {            $this->method = $method;            return $this;        }        throw new NotSupported("Method [$method] not supported.");    }    public function getEncType()    {        return $this->encType;    }    public function setEncType(string $encType)    {        if (in_array($encType, $this->enctypeAllowed) && $this->method === 'post') {            $this->encType = " enctype=\"{$encType}\"";            return $this;        } elseif (in_array($encType, $this->enctypeAllowed) && $this->method !== 'post') {            throw new NotSupported("Form-data encoding only works with method=\"post\" [$encType] not supported.");        }        throw new NotSupported("Enctype [$encType] not supported.");    }    public function getTarget()    {        return $this->target;    }    public function setTarget(string $target)    {        if (in_array($target, $this->targetAllowed)) {            $this->target = " target=\"{$target}\"";            return $this;        }        throw new NotSupported("Target [$target] not supported.");    }    public function getAutoComplete()    {        return $this->autoComplete;    }    public function setAutoComplete(string $autoComplete)    {        if (in_array($autoComplete, $this->autoCompleteAllowed)) {            $this->autoComplete = " autocomplete=\"{$autoComplete}\"";            return $this;        }        throw new NotSupported("Autocomplete [$autoComplete] not supported.");    }    public function getAcceptCharset()    {        return $this->acceptCharset;    }    public function setAcceptCharset(string $acceptCharset)    {        if (in_array($acceptCharset, $this->acceptCharsetAllowed)) {            $this->acceptCharset = " accept-charset=\"{$acceptCharset}\"";            return $this;        }        throw new NotSupported("Accept-charset [$acceptCharset] not supported.");    }    public function getName()    {        return $this->name;    }    public function setName(string $name)    {        if (in_array($name, $this->nameAllowed)) {            $this->name = " name=\"{$name}\"";            return $this;        }        throw new NotSupported("Name [$name] not supported.");    }    public function getRel()    {        return $this->rel;    }    public function setRel(string $rel)    {        if (in_array($rel, $this->relAllowed)) {            $this->name = " rel=\"{$rel}\"";            return $this;        }        throw new NotSupported("Rel [$rel] not supported.");    }    public function getNovalidate()    {        return $this->name;    }    public function setNovalidate(string $novalidate)    {        if (in_array($novalidate, $this->novalidateAllowed)) {            $this->novalidate = " novalidate=\"{$novalidate}\"";            return $this;        }        throw new NotSupported("Novalidate [$novalidate] not supported.");    }    public function getShowElements()    {        return $this->showElements;    }    public function setShowElements($value)    {        if (($value[0]) && is_array($value[0])) {            $value = $value[0];        }        if ($this->showElements === []) {            $this->showElements = $value;        } else {            $goOn = true;            foreach ($value as $item) {                if (!in_array($item, $this->showElements)) {                    $goOn = false;                }            }            if ($goOn === true) {                $this->showElements = $value;            }        }        return $this;    }    public function getElementSize()    {        return $this->elementSize;    }    public function setElementSize($value)    {        if (in_array($value, $this->elementSizeAllowed)) {            $this->elementSize = $value;            return $this;        }        throw new NotSupported("Element size [$value] not supported. Allowed values are: ".implode($this->elementSizeAllowed));    }    public function setShowAllLabels($value)    {        if (is_bool($value)) {            $this->showAllLabels = $value;        }        return $this;    }    public function getShowAllValues()    {        return $this->showAllValues;    }    public function setShowAllValues($value)    {        if (is_bool($value)) {            $this->showAllLabels = $value;        }        return $this;    }    public function getLabelColWidth()    {        return $this->labelColWidth;    }    public function setLabelColWidth(int $colWidth)    {        if (is_numeric($colWidth)) {            $this->labelColWidth = $colWidth;        }        return $this;    }    public function &__get(string $name)    {        $accessor = "get" . ucfirst($name);        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {            $value = $this->$accessor();            return $value;        } elseif (property_exists($this, $name)) {            return $this->{$name};        } else {            dump("__get: ", $name);        }        throw new NotSupported("__get: property or method ".get_called_class()."::{$name} is not supported");    }    /**     * @param string $name     * @param $value     * @throws NotSupported     */    public function __set(string $name, $value)    {        $accessor = "set" . ucfirst($name);        if (method_exists($this, $accessor) && $accessor !== 'setShowElements') {            return $this->$accessor($value[0] ?? '');        } elseif (method_exists($this, $accessor)) {            return $this->$accessor($value);        } elseif (property_exists($this, $name)) {            return $this->$name = $value;        } else {            dump('__set ', $name, $value);        }        throw new NotSupported("__set: property or method ".get_called_class()."::{$name} is not supported");    }    public function __isset(string $name)    {        if (property_exists($this, $name)) {            return true;        }        return false;    }    public function __call(string $name, $arguments)    {        if (empty($arguments) && $this->__isset($name)) {            return $this->__get($name);        } elseif (!empty($arguments) && $this->__isset($name)) {            return $this->__set($name, $arguments);        }        throw new NotSupported('__call (' . $name . ') with args: (' . print_r($arguments, true) . ') is not supported.');    }}