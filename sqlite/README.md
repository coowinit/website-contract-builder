# PHP + SQLite 持久化部署版

本目录提供网站建设合同生成器的 **PHP + PDO + SQLite** 持久化版本。

它仍然只保存“当前这一份合同”，不提供合同列表、历史合同、客户管理、统计报表、CRM、多用户或在线签名。

> 这个版本的目标很明确：在合同沟通、修改、确认和打印期间，把当前合同稳定保存到服务器。

---

## 一、工作方式

正常流程：

```text
打开页面
↓
读取 SQLite 当前合同
↓
继续填写 / 修改
↓
LocalStorage 临时保护
↓
停止输入后自动同步 SQLite
↓
也可以点击“保存”立即写入服务器
↓
预览
↓
打印 / 保存 PDF
```

数据分工：

```text
SQLite
└─ 当前合同主数据

LocalStorage
└─ 浏览器临时草稿保护

JSON
└─ 手动备份 / 迁移 / 恢复

PDF
└─ 最终签署档案
```

---

## 二、当前仓库结构

在 GitHub 仓库中，本版本位于：

```text
website-contract-builder/
├─ assets/
│  ├─ css/
│  │  └─ contract.css
│  └─ js/
│     └─ contract.js
│
└─ sqlite/
   ├─ index.php
   ├─ README.md
   ├─ .htaccess
   ├─ api/
   │  ├─ bootstrap.php
   │  ├─ load.php
   │  └─ save.php
   └─ database/
      ├─ .htaccess
      └─ index.html
```

注意：

> **PHP 版现在和静态版共用根目录 `assets/`，所以单独部署 PHP 时必须把共享资源一起复制。**

---

## 三、推荐的独立部署结构

例如部署到：

```text
https://example.com/hetong/
```

服务器目录建议整理为：

```text
/hetong/
├─ index.php
├─ .htaccess
│
├─ assets/
│  ├─ css/
│  │  └─ contract.css
│  └─ js/
│     └─ contract.js
│
├─ api/
│  ├─ bootstrap.php
│  ├─ load.php
│  └─ save.php
│
└─ database/
   ├─ .htaccess
   └─ index.html
```

复制关系：

```text
仓库 /assets/
→ 服务器 /hetong/assets/

仓库 /sqlite/index.php
→ 服务器 /hetong/index.php

仓库 /sqlite/.htaccess
→ 服务器 /hetong/.htaccess

仓库 /sqlite/api/
→ 服务器 /hetong/api/

仓库 /sqlite/database/
→ 服务器 /hetong/database/
```

然后直接访问：

```text
https://example.com/hetong/
```

---

## 四、为什么 `index.php` 不需要改资源路径

当前 `index.php` 会自动判断资源位置：

```php
$assetBase = is_dir(__DIR__ . '/assets') ? 'assets' : '../assets';
```

因此支持：

### 仓库开发结构

```text
website-contract-builder/
├─ assets/
└─ sqlite/
   └─ index.php
```

此时读取：

```text
../assets/
```

### 独立 PHP 部署结构

```text
/hetong/
├─ assets/
└─ index.php
```

此时读取：

```text
assets/
```

所以把 PHP 版单独迁移到其他目录或其他网站时，只要保留推荐目录结构即可。

---

## 五、服务器要求

推荐环境：

```text
PHP 8.x
PDO
PDO_SQLite
```

并确保：

```text
database/
```

目录具有 PHP 写入权限。

第一次运行时会自动创建：

```text
database/contract.sqlite
```

---

## 六、SQLite 保存模型

当前只保存一条记录：

```text
id = 1
```

主要结构：

```text
current_contract
├─ id
├─ data_json
└─ updated_at
```

整份合同以 JSON 保存到：

```text
data_json
```

因此后续增加普通表单字段时，通常：

```text
不需要新增 SQLite 字段
不需要做数据库迁移
```

这是当前结构保持简单的重要原因。

---

## 七、迁移到另一台服务器

### 方式 A：直接迁移数据库

如果希望保留当前正在填写的合同：

复制：

```text
旧服务器/database/contract.sqlite
```

到：

```text
新服务器/database/contract.sqlite
```

然后确认：

```text
database/ 可写
```

即可继续使用。

### 方式 B：使用 JSON 迁移

如果不想直接复制 SQLite 文件：

```text
旧系统导出 JSON
↓
部署新系统
↓
打开新系统
↓
导入 JSON
↓
点击保存
↓
写入新 SQLite
```

这种方式更容易人工确认数据是否正确。

### 方式 C：新系统重新开始

如果不需要旧合同：

不要复制：

```text
contract.sqlite
```

首次访问时系统会自动建立新的数据库。

---

## 八、以后更新程序时怎么做

### 只修改样式

更新：

```text
assets/css/contract.css
```

同时建议更新 `index.php` 中资源版本号，例如：

```text
contract.css?v=1.2.0-20260929-4
```

### 只修改前端功能

更新：

```text
assets/js/contract.js
```

必要时同步递增：

```text
contract.js?v=...
```

### 修改合同 HTML 结构

PHP 版修改：

```text
index.php
```

如果这些修改也要回到 GitHub 静态版，则同时修改仓库根目录：

```text
/index.html
```

### 修改 SQLite 接口

更新：

```text
api/
```

普通表单字段增加一般不需要修改数据库结构。

---

## 九、共享 CSS / JS 的维护原则

现在最重要的维护原则是：

```text
公共样式
→ assets/css/contract.css

公共前端逻辑
→ assets/js/contract.js
```

不要再把同一套 CSS / JS 分别复制回：

```text
index.html
index.php
```

这样可以避免以后出现：

```text
静态版修好了
PHP 版忘记修
```

或者：

```text
两个版本打印效果不一致
```

---

## 十、缓存说明

CSS / JS 使用查询版本号，例如：

```text
contract.css?v=1.2.0-20260929-4
contract.js?v=1.2.0-20260929
```

如果你更新了样式或 JS，但页面仍然显示旧效果，优先：

```text
递增资源版本号
```

而不是只依赖浏览器强制刷新。

如果前面还有 Cloudflare、主机缓存或 CDN，这种方式尤其有效。

---

## 十一、数据安全

合同可能包含：

- 姓名
- 电话
- 邮箱
- 地址
- 身份证号
- 统一社会信用代码
- 合同金额

因此建议：

- 部署在 Basic Auth 或其他访问控制之后
- 保留 `database/.htaccess`
- 不要把 `contract.sqlite` 放进公开仓库
- 不要把真实合同 JSON 放进公开仓库
- 正式签署完成后保存 PDF

---

## 十二、备份建议

最简单的备份组合：

```text
contract.sqlite
+
导出的 JSON
+
最终 PDF
```

其中：

- SQLite：继续编辑最方便
- JSON：迁移和人工恢复最方便
- PDF：最终签署归档

---

## 十三、打印相关说明

当前 PHP 版和静态版共用同一套打印 CSS / JS，因此以下修复会同步生效：

- 长文本完整打印
- 特别约定打印镜像
- 语言版本自动换行
- 参考网站完整显示
- 空白日期不显示“年/月/日”
- 甲方 / 乙方确认区双列打印
- 金额和付款区域打印对齐
- 微软雅黑统一字体

修改打印样式时只需要优先维护：

```text
assets/css/contract.css
```

---

## 十四、本版本明确不做

- 多合同列表
- 历史合同
- 客户管理
- 搜索
- 状态筛选
- 统计报表
- 多用户
- 在线签名
- CRM
- 历史版本管理

当前只解决：

> 当前合同的持续保存、修改、确认、迁移与最终打印。

---

## 十五、部署完成后的检查

建议依次确认：

- [ ] 页面能正常打开
- [ ] CSS / JS 正常加载
- [ ] `database/contract.sqlite` 能自动创建
- [ ] 修改字段后能自动保存
- [ ] 点击“保存”正常
- [ ] 刷新页面后数据仍存在
- [ ] JSON 导入 / 导出正常
- [ ] PDF 打印正常
- [ ] `database/contract.sqlite` 无法通过浏览器直接访问

全部通过后再投入正式使用。
