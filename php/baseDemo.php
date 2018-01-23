<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2018/1/23 18:35
 * description:
 */

/**
 * 区块
 * Class Block
 */
class Block
{
    /**
     * @var string 时间
     */
    public $timestamp;

    /**
     * @var integer 索引
     */
    public $index;

    /**
     * @var string 数据
     */
    public $data;

    /**
     * @var string 上一个哈希值
     */
    public $prevHash;

    /**
     * @var string 当前哈希
     */
    public $hash;

    public function __construct($index, $timestamp, $data, $prevHash = '')
    {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->prevHash = $prevHash;
        $this->hash = $this->calculateHash();
    }

    /**
     * 加密算法
     * @return string
     */
    public function calculateHash()
    {
        return hash('sha256', $this->index . $this->prevHash . $this->timestamp . json_encode($this->data));
    }

}

/**
 * 区块链
 * Class BlockChain
 */
class BlockChain
{

    /**
     * @var Block[]
     */
    public $chain = [];

    public function __construct()
    {
        $this->chain = [$this->createGenesisBlock()];
    }

    /**
     * 创世区块
     * @return Block
     */
    public function createGenesisBlock()
    {
        return new Block(0, '2017-01-23', 'forecho', '0');
    }

    /**
     * 获取最新的区块
     * @return Block|mixed
     */
    public function getLatestBlock()
    {
        return $this->chain[count($this->chain) - 1];
    }

    /**
     * 添加区块
     * @param Block $newBlock
     */
    public function addBlock(Block $newBlock)
    {
        $newBlock->prevHash = $this->getLatestBlock()->hash;
        $newBlock->hash = $newBlock->calculateHash();
        array_push($this->chain, $newBlock);
    }

    /**
     * 验证区块链
     * @return bool
     */
    public function isChainValid()
    {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $prevBlock = $this->chain[$i - 1];

            if ($currentBlock->hash !== $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->prevHash !== $prevBlock->hash) {
                return false;
            }
        }
        return true;
    }
}

// test
$blockChain = new BlockChain();
$blockChain->addBlock(new Block(1, '2017-02-23', ['amount' => 1]));
$blockChain->addBlock(new Block(2, '2017-03-23', ['amount' => 3]));
$blockChain->addBlock(new Block(3, '2017-04-23', ['amount' => 20]));


print_r($blockChain);
echo "区块链验证通过吗？" . ($blockChain->isChainValid() ? '通过' : '失败') . PHP_EOL;

$blockChain->chain[1]->data = ['amount' => 2];
$blockChain->chain[1]->hash = $blockChain->chain[1]->calculateHash();

echo "区块链验证通过吗？" . ($blockChain->isChainValid() ? '通过' : '失败') . PHP_EOL;

/**
 * 以上输出结果
 * BlockChain Object
 * (
 *  [chain] => Array
 *      (
 *          [0] => Block Object
 *              (
 *                  [timestamp] => 2017-01-23
 *                  [index] => 0
 *                  [data] => forecho
 *                  [prevHash] => 0
 *                  [hash] => 8862e987005e1ebf5f72488a41faba2b00deba05fbed894da742a1dd572dcafe
 *              )
 *
 *          [1] => Block Object
 *              (
 *                  [timestamp] => 2017-02-23
 *                  [index] => 1
 *                  [data] => Array
 *                      (
 *                          [amount] => 1
 *                      )
 *
 *                  [prevHash] => 8862e987005e1ebf5f72488a41faba2b00deba05fbed894da742a1dd572dcafe
 *                  [hash] => 19aad00d07e897fb110b15cf3b26a8602a70796c964586f14e78e4c2fee8c14c
 *              )
 *
 *          [2] => Block Object
 *              (
 *                  [timestamp] => 2017-03-23
 *                  [index] => 2
 *                  [data] => Array
 *                      (
 *                          [amount] => 3
 *                      )
 *
 *                  [prevHash] => 19aad00d07e897fb110b15cf3b26a8602a70796c964586f14e78e4c2fee8c14c
 *                  [hash] => d4d41ec0d43bde09e8d794dd7ba80f77bed5a693724c7ee382092be8df740c29
 *              )
 *
 *          [3] => Block Object
 *              (
 *                  [timestamp] => 2017-04-23
 *                  [index] => 3
 *                  [data] => Array
 *                      (
 *                          [amount] => 20
 *                      )
 *
 *                  [prevHash] => d4d41ec0d43bde09e8d794dd7ba80f77bed5a693724c7ee382092be8df740c29
 *                  [hash] => 90f13918cba4bd89078e058b6d69f3eab00bbd3c71a825c1113efff523006339
 *              )
 *
 *      )
 *
 *)
 *区块链验证通过吗？通过
 *区块链验证通过吗？失败
 *
 */