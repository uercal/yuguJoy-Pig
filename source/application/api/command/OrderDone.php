<?php
namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Order;
use app\common\model\Deduct as DeductModel;


class OrderDone extends Command
{

    protected function configure()
    {
        $this->setName('OrderDone')->setDescription('检查订单是否完结');
    }


    protected function execute(Input $input, Output $output)
    {
        $model = new DeductModel;
        $res = $model->checkOrder();
        $output->writeln($res['msg']);

    }

}