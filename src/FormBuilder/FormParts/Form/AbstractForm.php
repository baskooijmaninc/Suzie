<?phpnamespace KooijmanInc\Suzie\FormBuilder\FormParts\Form;use KooijmanInc\Suzie\Exception\NotSupported;abstract class AbstractForm implements FormInterface{    /**     * @var string     */    protected string $id;    protected string $action = '/';    protected string $method = 'post';    protected string $target = '_self';    protected string $autoComplete = 'off';    protected string $acceptCharset = 'utf-8';    protected string $enctype = 'application/x-www-form-urlencoded';    protected ?string $name = null;    protected ?string $rel = null;    protected bool $novalidate = false;    private array $methodAllowed = ['get', 'post', 'put', 'delete'];    private array $targetAllowed = ['_blank', '_self', '_parent', '_top', 'framename'];    private array $autoCompleteAllowed = ['off', 'on'];    private array $acceptCharsetAllowed = ['utf-8', 'utf-16', 'windows-1252', 'iso-8859-1'];    private array $enctypeAllowed = ['application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'];    private array $novalidateAllowed = [true, false];    private array $relAllowed = ['external', 'help', 'license', 'next', 'nofollow', 'noopener', 'noreferrer', 'opener', 'prev', 'search'];    public function __construct(string $id)    {        $this->id = $id.'-form';    }    /**     * @param string $action     * @return AbstractForm     */    public function setAction(string $action): AbstractForm    {        $this->action = $action;        return $this;    }    /**     * @param string $method     * @return $this     * @throws NotSupported     */    public function setMethod(string $method): AbstractForm    {        if (in_array($method, $this->methodAllowed, true)) {            $this->method = $method;            return $this;        }        throw new NotSupported("Value `$method` not supported for attribute method. Allowed values are: " . implode(', ', $this->methodAllowed));    }    public function getFormAttributes()    {        return $this;    }    public function __set(string $name, $value)    {        $accessor = "set".ucfirst($name);        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {            return $this->$accessor($value);        } else {            dump('Still to do: __set: ', $name, $value);        }    }    public function &__get(string $name)    {dump('__get: ', $name);    }    public function __call(string $name, array $arguments)    {//dump('__call: ', $name, $arguments);        if ($arguments !== []) {            return $this->__set($name, ...$arguments);        } elseif (!empty($arguments)) {            dump('not empty check set');        } else {            dump('undefined');        }        throw new NotSupported("__call ($name with arguments: ".implode($arguments).") in `".get_called_class()."` is not supported!");    }}