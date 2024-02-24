<?phpnamespace KooijmanInc\Suzie\FormBuilder;use KooijmanInc\Suzie\Exception\NotSupported;use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;interface FormBuilderInterface extends FormCollectorInterface{    /**     * @return string     */    public function getUuid(): string;    /**     * @return FormInterface     */    public function getForm(): FormInterface;    public function getCompleteForm();    public function getFormElements();    public function setFormElements(array $elements);    public function getFormElementsValidation();    public function setFormElementsValidation(array $baseElements);    public function setElements();    /**     * @param string $name     */    public function &__get(string $name);    /**     * @param $name     * @param $value     * @return mixed     * @throws NotSupported     */    public function __set($name, $value);    public function __isset(string $name);    public function toBeSetInputs(array $inputs): void;    public function getFormInput(...$input);    public function getFormSelect();}