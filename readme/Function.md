# 全局函数声明

#### 通用函数 src/Helper/Common.php
- 变量检查：是否为 json
  ```
  is_json( [mixed]:变量 );
  ```
  - return [boolean]:判断结果
- 变量检查：是否为 UUID
  ```
  is_uuid( [mixed]:变量 );
  ```
  - return [boolean]:判断结果
- 生成 UUID
  ```
  uuid();
  ```
  - return [string]:UUID
- 强制转为字符串
  ```
  toString( [mixed]:变量 );
  ```
  - return [string]:转换后的内容
- 路径保护式创建
  ```
  inFolder( [string]:目录或文件路径, [number]|0777:创建权限 );
  ```
  - return [string]:传入的路径
- 删除目录
  ```
  delFolder( [string]:目录路径 );
  ```
  - return [boolean]:删除结果
- 复制目录
  ```
  copyFolder( [string]:源目录路径, [string]:目标目录路径 );
  ```
  - return [boolean]:复制结果
- 时间值转换
  ```
  // 转换内容为 null 则输出当前时间
  toDate( [number|string]|null:转换内容 );
  ```
  - return [string]:格式化时间
- 参数序列化
  ```
  toValue( [mixed]:序列化值, [boolean]:是否为保存模式 );
  ```
  - return [mixed]:序列化或反序列化后的参数
- 字符串转数组
  ```
  toArray( [string]:转换字符串 );
  ```
  - return [array]数组
- 判断是否为公开方法
  ```
  isPublic( [object]:对象, [string]:方法名称 );
  ```
  - return [boolean]:判断结果
- 判断内容是否以指定内容开始
  ```
  startsWith( [string]:内容, [string]:开始内容 );
  ```
  - return [boolean]:判断结果
- 判断内容是否以指定内容结束
  ```
  endsWith( [string]:内容, [string]:结束内容 );
  ```
  - return [boolean]:判断结果
- 加密一段内容
  ```
  // init 完成后 $key === null 将使用 env( 'APP_KEY', 'DefaultKey' )
  encrypt( [string]:加密文本, [string]|null:加密密钥 );
  ```
  - return [string]:加密后内容
- 解密一段内容
  ```
  // init 完成后 $key === null 将使用 env( 'APP_KEY', 'DefaultKey' )
  decrypt( [string]:解密文本, [string]|null:解密密钥 );
  ```
  - return [string]:解密后内容
- 哈希一个参数
  ```
  // init 完成后将使用 env( 'APP_KEY', 'DefaultKey' ) 混淆
  h( [string]:传入的内容 );
  ```
  - return [string|null]:返回哈希后的字符串或 null
- 显示调试信息
  ```
  dd( [mixed]:调试内容, [boolean]|true:是否终止程序 );
  ```
  - return [void]:无返回值
- 返回忽略标识
  ```
  // 固定返回 '[THE_PROGRAM_DID_NOT_EXECUTE_ANYTHING]'
  null();
  ```
  - return [string]:忽略标识
- 获取 TODU-IO 组件目录
  ```
  ToduPath( [string]|'':子路径 );
  ```
  - return [string]:组件目录
