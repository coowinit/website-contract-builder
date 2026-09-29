# 🧾 网站建设合同生成器

一个面向网站建设项目的轻量级合同填写、保存、确认与打印工具。

当前仓库同时提供：

- **静态版**：根目录 `index.html`
- **PHP + SQLite 版**：`sqlite/index.php`
- **共享前端资源**：`assets/css/contract.css` + `assets/js/contract.js`

两套页面共用同一套前端样式与业务脚本，区别主要在于数据持久化方式：

```text
静态版
└─ LocalStorage + JSON

PHP / SQLite 版
└─ SQLite + LocalStorage + JSON
```

> 当前目标不是做 CRM 或合同管理系统，而是把网站建设项目中的范围、价格、周期、修改、验收、维护和交付说清楚，并稳定生成可打印的 A4 / PDF 合同。

---

## 一、项目定位

适用于：

- WordPress 网站建设
- 企业官网
- 外贸网站
- 产品展示网站
- 品牌官网
- 网站改版项目
- 个人或公司承接的网站建设项目

合同结构：

```text
9 个核心条款
+
附件一：项目需求与报价确认单
```

核心流程：

```text
填写
↓
保存
↓
确认
↓
打印 / PDF
↓
双方签署
```

---

## 二、当前代码结构

```text
website-contract-builder/
├─ index.html                  # 静态版入口 / GitHub Pages
├─ README.md
├─ VERSION
├─ .gitignore
├─ .gitattributes
│
├─ assets/                     # 两个版本共用
│  ├─ css/
│  │  └─ contract.css          # 页面 + 打印样式
│  └─ js/
│     └─ contract.js           # 表单、计算、打印、JSON、保存逻辑
│
└─ sqlite/
   ├─ index.php                # PHP / SQLite 版入口
   ├─ README.md                # PHP 独立部署说明
   ├─ .htaccess
   │
   ├─ api/
   │  ├─ bootstrap.php         # SQLite 初始化 / 公共响应逻辑
   │  ├─ load.php              # 读取当前合同
   │  └─ save.php              # 保存当前合同
   │
   └─ database/
      ├─ .htaccess             # 禁止数据库被 Web 直接访问
      └─ index.html
```

第一次运行 SQLite 版时会自动创建：

```text
sqlite/database/contract.sqlite
```

该文件属于运行时数据，不提交到 GitHub。

---

## 三、共享前端架构

### 1. 为什么要共享 CSS / JS

早期静态版和 PHP 版各自包含一份前端代码，修改打印、日期、textarea 或字段联动时需要改两次。

现在已经改为：

```text
assets/css/contract.css
assets/js/contract.js
        │
        ├─ index.html
        └─ sqlite/index.php
```

因此后续以下功能原则上只维护一份：

- 页面样式
- A4 / PDF 打印样式
- 金额计算
- 首付款比例联动
- 表单字段同步
- 动态页面 / 栏目行
- textarea 自动高度
- 打印前状态同步
- 长文本打印镜像
- 空白日期打印处理
- LocalStorage
- JSON 导入 / 导出

### 2. PHP 版额外负责什么

PHP 版只额外增加服务器持久化：

```text
api/load.php
api/save.php
SQLite
```

也就是说：

> **业务前端共用，数据持久化方式不同。**

---

## 四、两个版本如何选择

### 静态版

入口：

```text
/index.html
```

适合：

- GitHub Pages
- 本地直接打开
- 临时填写
- 无 PHP 环境
- 只依赖浏览器保存

数据：

```text
LocalStorage
+
JSON 手动备份
```

### PHP + SQLite 版

入口：

```text
/sqlite/index.php
```

适合：

- 部署到自己的 PHP 主机
- 合同沟通过程中持续保存
- 多次关闭 / 打开页面继续填写
- 希望数据保存在服务器

数据：

```text
SQLite
+
LocalStorage 临时保护
+
JSON 备用
```

> SQLite 版仍然只保存“当前这一份合同”，不提供合同列表、历史合同、客户管理、搜索、CRM 或多用户功能。

---

## 五、PHP 独立部署：最重要的说明

### 不要只复制 `sqlite/` 后忽略 `assets/`

当前 PHP 页面依赖共享资源：

```text
assets/css/contract.css
assets/js/contract.js
```

因此把 PHP 版单独部署到服务器时，推荐整理成：

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

来源对应关系：

```text
仓库根目录 assets/
        ↓
服务器 /hetong/assets/

仓库 sqlite/index.php
        ↓
服务器 /hetong/index.php

仓库 sqlite/api/
        ↓
服务器 /hetong/api/

仓库 sqlite/database/
        ↓
服务器 /hetong/database/
```

然后访问：

```text
https://example.com/hetong/
```

### `index.php` 为什么既能在仓库里运行，也能单独部署

当前 `sqlite/index.php` 会自动判断资源位置：

```php
$assetBase = is_dir(__DIR__ . '/assets') ? 'assets' : '../assets';
```

因此支持两种目录结构：

```text
A. 仓库结构
website-contract-builder/
├─ assets/
└─ sqlite/
   └─ index.php

B. PHP 独立部署
/hetong/
├─ assets/
└─ index.php
```

独立部署时只要把 `assets/` 一并复制到 PHP 目录即可，不需要修改 PHP 代码。

---

## 六、服务器迁移说明

### 场景 A：迁移程序，但不保留当前合同数据

复制：

```text
index.php
.htaccess
assets/
api/
database/.htaccess
database/index.html
```

不要复制旧的：

```text
contract.sqlite
```

首次访问后会自动创建新的数据库。

### 场景 B：迁移程序，并保留当前合同

除了程序文件外，把旧服务器中的：

```text
database/contract.sqlite
```

一起复制到新服务器：

```text
/新目录/database/contract.sqlite
```

迁移后确认 `database/` 可写。

### 场景 C：不想直接迁移数据库

也可以：

```text
旧系统导出 JSON
↓
新系统初始化
↓
导入 JSON
↓
点击保存
↓
写入新 SQLite
```

这种方式最直观，也适合跨环境迁移。

---

## 七、更新 PHP 独立版时该复制哪些文件

日常升级不一定需要整套覆盖。

### 只改页面样式

更新：

```text
assets/css/contract.css
```

同时更新 `index.php` 中 CSS 的版本参数，例如：

```text
contract.css?v=1.2.0-20260929-4
```

### 只改前端逻辑

更新：

```text
assets/js/contract.js
```

如果浏览器缓存明显，也同步递增 JS 查询版本号。

### 修改合同正文或附件 HTML

静态版和 PHP 版页面结构仍然分别位于：

```text
index.html
sqlite/index.php
```

如果正文结构有变化，应同步修改两处。

### 修改 SQLite 保存接口

更新对应：

```text
sqlite/api/
```

通常普通字段增加不需要修改 SQLite 表结构，因为整份合同数据以 JSON 保存。

---

## 八、资源缓存版本号

为了避免浏览器或 CDN 使用旧 CSS / JS，页面资源使用查询版本号，例如：

```html
contract.css?v=1.2.0-20260929-4
contract.js?v=1.2.0-20260929
```

当 CSS / JS 有明显修改时，建议递增版本参数。

需要同步检查：

```text
index.html
sqlite/index.php
```

如果 PHP 独立版部署在 Cloudflare、主机缓存或其他 CDN 后面，更新版本号通常比反复清缓存更稳定。

---

## 九、SQLite 保存模型

数据库只保存当前唯一合同：

```text
id = 1
```

核心关系：

```text
current_contract
├─ id = 1
├─ data_json
└─ updated_at
```

合同所有字段整体保存到：

```text
data_json
```

因此新增普通字段时，一般不需要数据库迁移。

数据职责：

```text
SQLite
└─ 服务器端当前合同主数据

LocalStorage
└─ 浏览器临时草稿保护

JSON
└─ 手动备份 / 迁移 / 恢复

PDF
└─ 最终签署档案
```

---

## 十、当前合同能力

正文包含：

```text
第一条   项目内容与范围
第二条   合同金额与付款
第三条   项目周期
第四条   双方责任
第五条   修改与需求变更
第六条   验收与上线
第七条   第三方服务与知识产权
第八条   交付与售后
第九条   违约、通知与争议
```

附件一用于确认：

- 项目名称
- 网站域名
- 网站类型 / 建站系统
- 语言版本
- 参考网站
- 制作周期
- 免费修改轮次
- 质保时间
- 合同金额 / 付款方式
- 后续维护方式
- 页面 / 栏目清单
- 功能清单
- 特别约定
- 双方确认

后续维护支持：

```text
乙方持续维护
或
完整技术交付
```

---

## 十一、打印设计

打印目标：

```text
A4 / PDF
```

当前已针对浏览器打印做过专门处理：

- 长文本自动完整输出
- “特别约定”使用打印文本镜像，避免 textarea 截断
- “语言版本 / 参考网站”打印时完整换行
- 空白确认日期不显示浏览器默认“年/月/日”
- 附件确认区按甲方 / 乙方双列打印
- 金额、百分比、费用说明等打印对齐优化
- 页面与打印统一使用微软雅黑
- 合同条款标题统一加粗显示

由于不同浏览器的打印引擎仍可能存在细微差异，正式 Release 前建议至少用 Chrome / Edge 做一次 PDF 回归检查。

---

## 十二、打印回归检查建议

每次修改打印 CSS 后，重点检查：

- [ ] 合同标题、条款标题字体正常
- [ ] 合同编号 / 日期 / 项目名称正常
- [ ] 甲乙双方信息完整
- [ ] `%` 紧跟首付款比例
- [ ] 费用说明完整且左对齐
- [ ] 条款中的数字输入正常
- [ ] 语言版本完整换行
- [ ] 参考网站全部显示
- [ ] 页面 / 栏目清单不截断
- [ ] 特别约定完整
- [ ] 空白日期不出现“年/月/日”
- [ ] 甲方 / 乙方确认区保持双列
- [ ] 签署区不被分页截断

---

## 十三、静态版使用

根目录：

```text
index.html
```

可直接：

- 双击本地打开
- 上传到 GitHub Pages
- 部署到普通静态空间

数据保存在：

```text
LocalStorage
```

同时支持：

- JSON 导出
- JSON 导入
- A4 打印
- 浏览器保存 PDF

---

## 十四、GitHub Pages

推荐：

```text
Branch: main
Folder: /root
```

GitHub Pages 直接加载：

```text
/index.html
```

共享资源：

```text
/assets/
```

`sqlite/` 不影响静态页面使用。

---

## 十五、数据安全

合同中可能包含：

- 姓名
- 电话
- 邮箱
- 地址
- 身份证号
- 统一社会信用代码
- 合同金额

因此建议：

- PHP / SQLite 版部署在 Basic Auth 或其他访问控制之后
- 不要把真实 SQLite 数据库提交到 GitHub
- 不要把真实合同 JSON 提交到公开仓库
- 正式合同完成后保存 PDF
- JSON 只作为备用或迁移文件

仓库 `.gitignore` 已用于避免运行时数据误提交。

---

## 十六、技术栈

```text
HTML
CSS
Vanilla JavaScript
LocalStorage
PHP
PDO
SQLite
```

不依赖：

- Node.js
- npm
- React
- Vue
- Laravel
- MySQL
- 构建工具

---

## 十七、版本状态

当前 `VERSION`：

```text
1.1.3
```

已发布版本：

### v1.0.0

首个正式合同生成器版本。

### v1.0.1

优化 A4 / PDF 打印、分页、签署区及附件布局。

### v1.1.0

新增 PHP + SQLite 持久化版本。

### v1.1.1

完善费用说明、持续维护 / 完整交付及打印确认逻辑。

### v1.1.2

优化特别约定、确认字段、空白日期等打印细节。

### v1.1.3

继续优化合同金额与付款区域的打印布局。

当前 `main` 分支在 v1.1.3 基础上已经完成共享 CSS / JS 的结构优化，作为下一版本的开发基础；正式 Release 前再统一更新 `VERSION` 与 Release 说明。

---

## 十八、后续维护原则

### 前端公共逻辑优先改共享资源

```text
CSS → assets/css/contract.css
JS  → assets/js/contract.js
```

不要重新在 `index.html` 和 `index.php` 中复制两份相同的大段 CSS / JS。

### 页面结构修改仍需双端同步

如果修改：

- 合同正文
- 附件结构
- 新增 / 删除 HTML 字段

请同时检查：

```text
index.html
sqlite/index.php
```

### 数据库保持简单

目前不计划加入：

- 合同列表
- 历史合同
- 客户管理
- CRM
- 报表
- 多用户
- 在线签名

只有真实需求出现时再扩展。

---

## 十九、免责声明

本项目提供的是网站建设合同模板与填写工具，用于帮助双方明确：

- 项目范围
- 价格
- 工期
- 修改
- 验收
- 交付
- 售后

本项目不构成法律意见，也不能替代专业律师针对具体项目、合同主体、交易金额及适用法律所提供的法律服务。

对于金额较大、跨境、复杂知识产权或其他高风险项目，建议正式签署前由专业律师审核。
