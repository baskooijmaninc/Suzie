<?phpnamespace KooijmanInc\Suzie\Console\Command\Make\Generator;use KooijmanInc\Suzie\Exception\DuplicateModel;use KooijmanInc\Suzie\Model\Connection\ConnectionFactory;class ClassBuilder{    /**     * @var ConnectionFactory     */    protected ConnectionFactory $connectionFactory;    /**     * @var string     */    protected string $modelPath;    /**     * @var string     */    protected string $className;    /**     * @var array     */    protected array $modelTypes;    /**     * @var string     */    protected string $classType;    /**     * @var string     */    protected string $tableName;    /**     * @var string     */    protected string $database;    /**     * @var string     */    protected string $completeClass;    /**     * @param string $namespace     * @return bool     * @throws DuplicateModel     */    protected function writeModel(string $namespace): bool    {        $startNameSpace = str_replace('/', "\\", str_starts_with($this->modelPath, 'src') ? str_replace('src', 'App', $this->modelPath) : $this->modelPath);        $fullNamespace = $startNameSpace ."\\" . $this->classType ."\\".$namespace;        $path = $this->modelPath.'/'.$this->classType.'/'.$namespace.'/'.$this->className.'.php';        if (!file_exists($path)) {            $suzie = fopen($path, "w");            if ($suzie) {                if (fwrite($suzie, $this->buildClass($fullNamespace, $startNameSpace, $this->classType, $namespace, $this->className)) !== false && fclose($suzie) !== false) {                    //return true;                }            }        } else {            throw new DuplicateModel("Failed to create '{$this->className}' {$this->classType} model. The model exists!");        }exit();        return false;    }    private function buildClass(string $fullNamespace, string $startNamespace, string $classType, string $namespace, string $className)    {        dump($fullNamespace, $startNamespace, $classType, $namespace, $className);        $classContent = $this->getBlueprint();        if ($this->classType === 'DataAccess') {            $this->completeClass = sprintf("<?php\n\n" . $classContent, $fullNamespace, $className, $this->database, $this->tableName);        }        return (bool)$this->completeClass;    }    private function getBlueprint()    {        return file_get_contents(__DIR__ . "/templates/{}$this->classType}.txt");    }}