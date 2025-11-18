# Debug 调试工具

#### 调试助手 src/Helper/Debug.php
###### Todu\Helper\Debug
- 输出错误信息
  ```
  Debug::error( [string]:错误信息 );
  ```
  - return [void]:无返回值
- 显示调试信息
  ```
  Debug::show( [mixed]:调试内容, [boolean]|true:是否终止程序 );
  ```
  - return [void]:无返回值
- 记录日志信息
  ```
  Debug::log( [string]:日志名称, [mixed]:日志内容 );
  ```
  - return [bool]:是否写入成功