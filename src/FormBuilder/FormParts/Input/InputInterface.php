<?phpnamespace KooijmanInc\Suzie\FormBuilder\FormParts\Input;use KooijmanInc\Suzie\Exception\NotSupported;use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;interface InputInterface extends FormCollectorInterface{    /**     * @return string     */    public function getName(): string;    /**     * @param string $name     * @return $this     * @throws NotSupported     */    public function setName(string $name): static;    /**     * @return string     */    public function getFormElement(): string;    /**     * @param $element     * @return $this     * @throws NotSupported     */    public function setFormElement($element): static;    public function getShowElement();    public function setShowElement($element);    public function getShowLabel();    public function setShowLabel($label);    public function getDisplayName();    public function setDisplayName(string $displayName);    public function getDomain();    public function getClass();    public function setClass(string $class);    public function addClass(string $class);    public function getLockedValue();    public function setLockedValue(string $value);    public function getValue();    public function setValue($value);}