<?phpnamespace KooijmanInc\Suzie\FormBuilder\FormParts\Form;use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;interface FormInterface extends FormCollectorInterface{    public function getAction();    public function setAction(string $action);    public function getMethod();    public function setMethod(string $method);    public function getEncType();    public function setEncType(string $encType);    public function getTarget();    public function setTarget(string $target);    public function getAutoComplete();    public function setAutoComplete(string $autoComplete);    public function getAcceptCharset();    public function setAcceptCharset(string $acceptCharset);    public function getName();    public function setName(string $name);    public function getRel();    public function setRel(string $rel);    public function getNovalidate();    public function setNovalidate(string $novalidate);    public function getShowElements();    public function setShowElements($value);    /**     * @param string $name     */    public function &__get(string $name);    /**     * @param string $name     * @param $value     */    public function __set(string $name, $value);    public function __isset(string $name);}