# 核心驱动工具

#### 核心驱动器 src/Bootstrap.php
- 驱动器初始化状态
  ```
  boolean Bootstrap::$init;
  ```
- 初始化驱动器
  ```
  Bootstrap::init();
  ```
  - return [void]:无返回值
- 线程缓存工具
  ```
  Bootstrap::cache( [string]:缓存名称, [callable]:回调方法 );
  ```
  - return [mixed]:返回缓存数据

#### 系统通用函数 support/Helper/System.php
- 导入文件
  ```
  import( [string]|[array]:文件路径 );
  ```
  - return [mixed]:返回引入内容
- 查询配置信息
  ```
  // 这个函数在 init 完成后可直接使用 config/***.php 配置文件
  config( [string]|[null]:配置信息键, [mixed]|null:默认值 );
  ```
  - return [mixed]:配置信息内容