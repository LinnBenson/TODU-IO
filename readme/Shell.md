# Shell 工具及声明

#### Shell 工具 src/Helper/Shell.php
- 输出状态结果
  ```
  Shell::echo( [boolean]:状态, [mixed]:输出内容, [boolean]|true:是否直接输出 );
  ```
  - return [string]:返回输出内容
- 确认操作
  ```
  Shell::confirm( [string]:提示文本, [mixed]:确认结果, [mixed]|null:取消结果 );
  ```
  - return [mixed]:返回执行结果
- 显示进度条
  ```
  Shell::schedule( [number]:总数, [number]:当前数 );
  ```
  - return [void]:无返回值
- 输入选项菜单
  ```
  Shell::menu( [array]:菜单数据, [string]|null:直接执行方法 );
  ```
  - return [mixed]:执行项目结果
- 获取用户输入
  ```
  Shell::input( [string]|'Please enter:':提示文本 );
  ```
  - return [string]:用户输入内容
- 输出文本
  ```
  Shell::line( [mixed]:输出内容, [boolean]|true:是否直接输出 );
  ```
  - return [string]:返回输出内容
- 解析命令行参数
  ```
  Shell::validate( [array]:参数数组, [FILE|GET|POST]|null:数据类型 );
  ```
  - return [array|string]:获取的参数内容