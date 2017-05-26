## 消息队列

Polymer支持简单的消息队列

### 生产者(可以在任何地方使用下列代码)
``` 
$producer = $this->app->component('mq_producer');
$producer->produce(new DefaultMessage('EchoTime', [
   'time' => time(),
]), 'EchoTime');

```

### 消费者(在Task里面写入下列代码)

``` 
$consumer = app()->component('mq_consumer', ['receivers' => ['EchoTime' => new \CMS\Services\OtherService()]]);
$consumer->consume(app()->component('mq_factory')->create('EchoTime'));
```

### OtherService
```
use Bernard\Message\DefaultMessage;
use Polymer\Service\Service;

class OtherService extends Service
{
    public function EchoTime(DefaultMessage $message)
    {
       echo 'hello message queue' . $message->time;
    }
}
```




    