<?phpnamespace KooijmanInc\Suzie\Console\Command\Make;use KooijmanInc\Suzie\Console\Command\AbstractCommand;use KooijmanInc\Suzie\Console\Command\Make\Generator\ClassGenerator;use KooijmanInc\Suzie\Exception\DuplicateModel;use KooijmanInc\Suzie\Model\Connection\ConnectionFactory;use Symfony\Component\Console\Attribute\AsCommand;use Symfony\Component\Console\Input\InputArgument;use Symfony\Component\Console\Input\InputInterface;use Symfony\Component\Console\Output\OutputInterface;use Symfony\Component\Console\Question\ConfirmationQuestion;#[AsCommand(name: 'suzie:make:model', description: 'Make a Suzie model from Database', aliases: ['make:model', 'make:suzie'])]class Model extends AbstractCommand{    private ConnectionFactory $connectionFactory;    public function __construct(ConnectionFactory $connectionFactory)    {        $this->connectionFactory = $connectionFactory;        parent::__construct();    }    protected function configure()    {        parent::configure();        $this->addArgument('Name', InputArgument::REQUIRED, 'The name of the new Suzie model')->addArgument('Database', InputArgument::REQUIRED, 'The database name')->addArgument('Table', InputArgument::REQUIRED, 'The name of the table');    }    /**     * @param InputInterface $input     * @param OutputInterface $output     * @return int     */    protected function execute(InputInterface $input, OutputInterface $output): int    {        //$this->bootstrap($input, $output);        $classGenerator = new ClassGenerator($this->connectionFactory, 'src/Model');        if ($this->tableExists($input->getArgument('Database'), $input->getArgument('Table')) === false) {            $output->writeln("Table '{$input->getArgument('Table')}' does not exist!");            return 1;        }        $name = $input->getArgument('Name');        $namespace = '';        if (str_contains($name, '/')) {            $nameParts = explode('/', $input->getArgument('Name'));            $name = end($nameParts);            array_pop($nameParts);            $namespace = implode("/", $nameParts);        }        $fullNamespace = "App\Model\\Suzie\\$namespace\\$name";        $helper = $this->getHelper('question');        $question = new ConfirmationQuestion("<info>Is the Model namespace `$fullNamespace` correct for table `{$input->getArgument('Table')}` in database `{$input->getArgument('Database')}`?</info> yes/no:", false, '/^(y|j)/i');        if ($helper->ask($input, $output, $question)) {            $output->writeln("Creating classes ...");            $modelTypes = ['DataAccess', 'Entity', 'FormBuilder', 'Suzie'];            $classGenerator->setClassName($name);            $classGenerator->setModelTypes($modelTypes);            foreach ($modelTypes as $modelType) {                try {                    $classGenerator->setClassType($modelType)->setTableName($input->getArgument('Table'))->setDatabase($input->getArgument('Database'));                    if ($classGenerator->write($namespace ?? null)) {                        $output->writeln("Successfully create '$name' {$modelType} model!");                    } else {                        $output->writeln("Failed to create '$name' {$modelType} model. Unknown error!");                    }                    $this->checkConfigServices($output);                } catch (DuplicateModel $e) {                    $output->writeln("Failed to create '$name' {$modelType} model. The model exists!");                }            }        } else {            $output->writeln("Aborting ...");            return 1;        }        return 0;    }    /**     * @param string $database     * @param string $tableName     * @return bool     */    private function tableExists(string $database, string $tableName): bool    {        $this->connectionFactory->setDatabase($database);        $this->connectionFactory->connect();        return (bool)$this->connectionFactory->fetchAll("DESCRIBE `$tableName`");    }    private function checkConfigServices(OutputInterface $output)    {        if (file_exists('config/services.yaml')) {            $output->writeln("file found");            $file = 'config/services.yaml';            $searchFor = "src/Model/DataAccess";            $searchFor1 = "src/Model/Suzie";            $contents = file_get_contents($file);            $pattern = preg_quote($searchFor, '/');            $pattern = "/^.*$searchFor.*\$/m";            $pattern1 = preg_quote($searchFor1, '/');            $pattern1 = "/^.*$searchFor1.*\$/m";            $dataAccess = false;            $suzie = false;            if (preg_match_all($pattern, $contents, $matches)) {                $dataAccess = true;            }            if (preg_match_all($pattern1, $contents, $matches)) {                $suzie = true;            }            if ($dataAccess === false) {                $file = fopen('config/services.yaml', "a+");                $text = "\n    App\\Model\\DataAccess\\:\n        resource: '../src/Model/DataAccess/*\n        arguments:\n            \$debug: '%kernel.debug%'\n";                fwrite($file, $text);                fclose($file);            }            if ($suzie === false) {                $file = fopen('config/services.yaml', "a+");                $text = "\n    App\\Model\\Suzie\\:\n        resource: '../src/Model/Suzie/*\n        public: true\n        calls:\n            - [ setStopwatch, [ '@?Symfony\\Component\\Stopwatch\\Stopwatch' ] ]\n            - [ setDebug, [ '%kernel.debug%' ] ]";                fwrite($file, $text);                fclose($file);            }        }        exit();    }}