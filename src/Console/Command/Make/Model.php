<?phpnamespace KooijmanInc\Suzie\Console\Command\Make;use KooijmanInc\Suzie\Console\Command\AbstractCommand;use KooijmanInc\Suzie\Console\Command\Make\Generator\ClassGenerator;use KooijmanInc\Suzie\Model\Connection\ConnectionFactory;use Symfony\Component\Console\Attribute\AsCommand;use Symfony\Component\Console\Input\InputArgument;use Symfony\Component\Console\Input\InputInterface;use Symfony\Component\Console\Output\OutputInterface;use Symfony\Component\Console\Question\ConfirmationQuestion;#[AsCommand(name: 'suzie:make:model', description: 'Make a Suzie model from Database', aliases: ['make:model', 'make:suzie'])]class Model extends AbstractCommand{    private ConnectionFactory $connectionFactory;    public function __construct(ConnectionFactory $connectionFactory)    {        $this->connectionFactory = $connectionFactory;        parent::__construct();    }    protected function configure()    {        parent::configure();        $this->addArgument('Name', InputArgument::REQUIRED, 'The name of the new Suzie model')->addArgument('Database', InputArgument::REQUIRED, 'The database name')->addArgument('Table', InputArgument::REQUIRED, 'The name of the table');    }    /**     * @param InputInterface $input     * @param OutputInterface $output     * @return int     */    protected function execute(InputInterface $input, OutputInterface $output): int    {        //$this->bootstrap($input, $output);        $classGenerator = new ClassGenerator($this->connectionFactory, 'src/Model');        if ($this->tableExists($input->getArgument('Database'), $input->getArgument('Table')) === false) {            $output->writeln("Table '{$input->getArgument('Table')}' does not exist!");            return 1;        }        $name = $input->getArgument('Name');        $namespace = '';        if (str_contains($name, '/')) {            $nameParts = explode('/', $input->getArgument('Name'));            $name = end($nameParts);            array_pop($nameParts);            $namespace = implode("/", $nameParts);        }        $fullNamespace = "App\Model$namespace\\$name";        $helper = $this->getHelper('question');        $question = new ConfirmationQuestion("<info>Is the Model namespace `$fullNamespace` correct for table `{$input->getArgument('Table')}` in database `{$input->getArgument('Database')}`?</info> yes/no:", false);        if (!$helper->ask($input, $output, $question)) {        }        return 0;    }    /**     * @param string $database     * @param string $tableName     * @return bool     */    private function tableExists(string $database, string $tableName): bool    {        $this->connectionFactory->setDatabase($database);        $this->connectionFactory->connect();        return (bool)$this->connectionFactory->fetchAll("DESCRIBE `$tableName`");    }}