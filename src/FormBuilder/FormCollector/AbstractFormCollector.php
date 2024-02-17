<?phpnamespace KooijmanInc\Suzie\FormBuilder\FormCollector;use KooijmanInc\Suzie\Exception\NotSupported;use KooijmanInc\Suzie\FormBuilder\FormParts\Form\Form;use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;use KooijmanInc\Suzie\FormBuilder\FormParts\Input\Input;use KooijmanInc\Suzie\Object\FormObject\ObjectStorage;/** * Class AbstractFormCollector * @property string $id */abstract class AbstractFormCollector implements FormCollectorInterface{    /**     * @var string     */    protected string $id;    /**     * @var string     */    protected string $uuid;    /**     * @var FormInterface     */    protected FormInterface $form;    /**     * @var     */    protected $formElements;    /**     * @var array     */    private array $toBeSet = [];    public function __construct(string $id)    {        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);        $this->id = $id;        $this->form = new Form($this->id);    }    public function getFormElements()    {        return $this->formElements;    }    public function setFormElements(array $data = [], array $base = [])    {        $objectStorage = $this->createObjectStorageObject();        if ($base === [] && ($data[0]) && count($data[0]) === 2) {            $base = $data[0][1];            $data = $data[0][0];        }        foreach ($this->sanitizeFormElementsArray($data, $base) as $elements) {            $element = new Input($elements['id']);            $objectStorage->attach($element, $elements['formElement']);            $this->{$elements['formElement']} = $element;        }        $this->formElements = $objectStorage;        return $objectStorage;    }    /**     * @return FormInterface     */    public function getForm(): FormInterface    {        return $this->form;    }    public function &__get(string $name)    {        $accessor = "get".ucfirst($name);        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {            $value = $this->$accessor();            return $value;        } elseif (property_exists($this, $name)) {            return $this->$name;        }        throw new NotSupported("__get: property or method ".get_called_class()."::{$name} is not supported");    }    public function __set(string $name, $value)    {        $accessor = "set".ucfirst($name);        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {            return $this->$accessor($value);        } elseif (property_exists($this, $name) || in_array($name, $this->toBeSet)) {            return $this->{$name} = $value;        }        throw new NotSupported("__set: property or method ".get_called_class()."::{$name} is not supported");    }    /**     * @param string $name     * @param array $arguments     * @return mixed     * @throws NotSupported     */    public function __call(string $name, array $arguments)    {        if (empty($arguments) && $this->__isset($name)) {            return $this->__get($name);        } elseif (!empty($arguments)) {            return $this->__set($name, $arguments);        }        throw new NotSupported("__call (".get_called_class()."::{$name}) with args: (".print_r($arguments, true).") is not supported.");    }    public function __isset(string $name)    {        if (property_exists($this, $name)) {            return true;        }        return false;    }    protected function createObjectStorageObject()    {        return new ObjectStorage();    }    private function sanitizeFormElementsArray($data, $base)    {        //dump($data, $base);        foreach ($data as $k => $v) {            if (isset($base['id'])) {                $id = "{$base['id']}-{$k}";            }            $formElements[] = ['id' => $id, 'formElement' => $k];            $this->toBeSet[] = $k;        }        return $formElements ?? [];    }}