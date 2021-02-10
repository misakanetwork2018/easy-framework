# easy-framework
最简单的PHP框架

## 来源
抽卡姬代码剥离

## 特性
- 无Composer管理，适合传统小型项目
- 具有最简单的容器管理器，可根据类名获取对象（单例模式）
- 使用简单的?m=&a=模式访问相应模块的方法，预设地址重写
- 支持中间件，便于权限管理
- 内置Http组件，便于处理请求
- 内置DB类，便于执行SQL
- 内置配置管理，支持 . 分隔获取子项目
- 标准目录结构，符合PSR-4自动加载规范

## 缺点
- 一些自定义代码（如Module,Middleware）目录目前只能放在include中（因为要符合PSR-4自动加载规范，放在其他目录不合适）
- App::run方法目前没有外部手段可以做Hook，要重写一些功能只能直接改，或者为了同步上游代码，可以另外创建一个类并继承App类。
- 默认情况下，根目录暴露在用户访问区，可自行调整入口位置。
- App类必须手动加载 `require_once APP_ROOT . "/include/App.php";`

## 开源协议
[MIT](LICENSE)