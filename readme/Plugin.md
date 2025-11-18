# 插件支持及权限

#### 权限介绍
- 监听或修改系统启动结果
  - SYSTEM_STARTUP_RESULTS
  - 传入参数( [mixed]:系统启动结果 )
  - 回传方式( 获取最后的值覆盖传入参数，返回 null 则不处理 )
- 修改或增加系统语言包
  - LANGUAGE_PACK_LOADING
  - 传入参数( [string]:语言标识, [string]:语言包名称 )
  - 回传方式( 整合所有返回的数组包，返回 null 则不处理 )
- 修改或增加系统配置信息
  - CONFIGURATION_INFORMATION_QUERY
  - 传入参数( [string]:配置包名称 )
  - 回传方式( 整合所有返回的数组包，返回 null 则不处理 )

#### 插件基类 support/Slot/Plugin.php
###### Todu\Slot\Plugin
###### 需要 Bootstrap::init() 初始化后使用
- 插件 ID
  ```
  string $Plugin->id;
  ```
- 插件名称
  ```
  string $Plugin->name;
  ```
- 插件根目录
  ```
  string $Plugin->path;
  ```
- 插件版本号
  ```
  string $Plugin->version;
  ```
- 插件作者
  ```
  string $Plugin->author;
  ```
- 插件描述
  ```
  string $Plugin->description;
  ```
- 依赖的其他插件
  ```
  array $Plugin->rely;
  ```
- 兼容性声明
  ```
  // [ '最低支持', '最高支持' ]
  // * 代表不限制
  array $Plugin->compatible;
  ```
- 权限注册
  ```
  $Plugin->intervene( [string]:权限名称, [function|string]:运行方法 );
  ```
  - return [boolean]:挂载结果
- 文件引用信息
  ```
  $Plugin->import( [string|array]:需要引用的文件 );
  ```
  - return [mixed]:返回引用的结果
- 注册到自动加载
  ```
  $Plugin->autoload( [array]:需要注册的文件 );
  ```
  - return [boolean]:注册结果
- Config 配置
  ```
  // 自动调用 插件目录下的 config.php 和 config/plugin/{id}.php 文件
  $Plugin->config( [string]:键名, [mixed]|null:默认值 );
  ```
  - return [mixed]:键值