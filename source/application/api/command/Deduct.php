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
        $todayList = $model->checkDeduct();
        foreach ($todayList as $key => $value) {
            $output->writeln($value['deduct_time'] . '  ' . date('Y-m-d', $value['deduct_time']));
        }
        $output->writeln('今天是:' . date('Y-m-d', time()) . ',timestamp:' . strtotime(date('Y-m-d', time())));

    }

}