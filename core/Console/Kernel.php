<?php
namespace Core\Console;
use Core\Container\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Kernel extends Application {
    public function __construct(private Container $c, private string $basePath){ parent::__construct('NovaCore','1.0.0'); $this->add(new class($basePath) extends Command{ protected static $defaultName='make:module'; public function __construct(private string $basePath){parent::__construct();}
      protected function configure(): void {$this->addArgument('name', InputArgument::REQUIRED);} protected function execute(InputInterface $i, OutputInterface $o): int { $name=$i->getArgument('name'); $base=$this->basePath.'/modules/'.$name; foreach(['Controllers','Models','Views','Routes','Services','Middleware','Events','Database/Migrations','Database/Seeders','Config'] as $d){@mkdir($base.'/'.$d,0777,true);} file_put_contents($base.'/module.php',"<?php\nreturn ['name'=>'$name','enabled'=>true];\n"); $o->writeln("Module $name created"); return Command::SUCCESS; }});
    }
}
