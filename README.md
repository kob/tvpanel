# 骆驼IPTV管理后台

- 骆驼后台魔改版
- 兼容PHP7
- 去除无用的功能和代码

## 安装对接
- 下载代码
```bash
git clone https://github.com/GaHoKwan/tvpanel
```
- 新建并导入数据库
```bash
mysql -u用户名 -p密码 数据库名 < 数据库.sql
```
- 修改sql.php对接数据库
```bash
mysqli_connect("数据库地址" , "数据库用户" , "数据库密码" , "数据库名")
```

- 登入后台
```
http://{域名}/admin/userlogin.php
```
