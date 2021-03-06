<?php
namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Order;
use app\common\model\Deduct as DeductModel;
use app\common\model\AccountMoney;


class Deduct extends Command
{

    protected function configure()
    {
        $this->setName('DeductOrder')->setDescription('deduct-order');
    }


    protected function execute(Input $input, Output $output)
    {
        $model = new DeductModel;
        $res = $model->checkDeduct();
        $output->writeln($res['msg']);

    }

}