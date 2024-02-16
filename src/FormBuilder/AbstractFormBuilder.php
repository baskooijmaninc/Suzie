<?phpnamespace KooijmanInc\Suzie\FormBuilder;use KooijmanInc\Suzie\Exception\NotSupported;use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;use KooijmanInc\Suzie\FormBuilder\FormParts\Input\InputInterface;use KooijmanInc\Suzie\Object\FormObject\ObjectStorage;use KooijmanInc\Suzie\SuzieInterface;abstract class AbstractFormBuilder implements FormBuilderInterface{    /**     * @var string     */    protected string $uuid;    /**     * @var SuzieInterface     */    protected SuzieInterface $suzie;    /**     * @var FormCollectorInterface     */    protected FormCollectorInterface $formCollector;    /**     * @var array     */    protected array $toBeSetInputs = [];    /**     * @var FormInterface     */    protected FormInterface $form;    /**     * @var string     */    protected string $completeForm;    /**     * @param SuzieInterface $suzie     */    public function __construct(SuzieInterface $suzie)    {        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);        $this->suzie = $suzie;    }    public function getUuid(): string    {        return $this->uuid;    }    /**     * @return FormInterface     */    public function getForm(): FormInterface    {        return $this->formCollector->form;    }    public function getCompleteForm()    {        return $this->setCompleteForm();    }    public function setRaw(array $data, array $base, $id = null)    {        foreach ($data as $inputs => $input) {            $this->{$inputs} = new FormParts\Input\Input("$id-$inputs");        }        return $this;    }    public function &__get(string $name)    {        $accessor = "get" . ucfirst($name);        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {            $value = $this->$accessor();            return $value;        } elseif (property_exists($this, $name)) {            return $this->$name;        }        throw new NotSupported("__get: property or method ".get_called_class()."::{$name} is not supported");    }    public function __set(string $name, $value)    {        if (property_exists($this, $name)) {            return $this->$name = $value;        } elseif (!property_exists($this, $name) && array_key_exists($name, $this->toBeSetInputs)) {            return $this->$name = $value;        }        throw new NotSupported("__set: property or method ".get_called_class()."::{$name} is not supported");    }    public function __isset(string $name)    {        if (property_exists($this, $name)) {            return true;        }        return false;    }    /**     * @param array $inputs     * @return void     */    public function toBeSetInputs(array $inputs): void    {        foreach ($inputs as $key => $value) {            if (!isset($this->toBeSetInputs[$key])) {                $this->toBeSetInputs[$key] = $value;            }        }    }    public function getFormInput()    {        return "<input>";    }    public function getFormSelect()    {        $select = "<select>";        $select .= "</select>";        return $select;    }    public function setCompleteForm()    {        if (!isset($this->form)) {            $this->form = $this->getForm();        }        //dump($this->form);        $this->completeForm = "<form action=\"{$this->form->action}\" method=\"{$this->form->method}\"{$this->form->encType}{$this->form->target}{$this->form->autoComplete}{$this->form->acceptCharset}{$this->form->name}{$this->form->rel}{$this->form->novalidate}>";        foreach ($this->toBeSetInputs as $key => $value) {            if (property_exists($this, $key)) {                $inputElement = $this->setFormElement($this->$key);                $this->completeForm .= $inputElement;            }        }        $this->completeForm .= "</form>";        return $this->completeForm;    }    /**     * @param string $name     * @param array $arguments     * @return mixed     * @throws NotSupported     */    public function __call(string $name, array $arguments)    {        if (empty($arguments) && $this->__isset($name)) {            return $this->__get($name);        }        throw new NotSupported('__call (' . $name . ') with args: (' . print_r($arguments, true) . ') is not supported.');    }    protected function createObjectStorageObject()    {        return new ObjectStorage($this->suzie);    }    protected function setFormElement(InputInterface $input): string    {        if ($input->formElement === 'input') {            return $this->getFormInput();        } elseif ($input->formElement === 'select') {            return $this->getFormSelect();        } else {            return '';        }    }}