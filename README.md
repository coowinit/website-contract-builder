# 🧾 网站建设合同生成器

一个面向网站建设项目的轻量级合同填写、保存与打印工具。

当前仓库同时提供两个版本：

1. **静态版**：根目录 `index.html`
2. **SQLite 持久化版**：`/sqlite/`

两者使用同一套合同结构和打印样式，但适用场景不同。

---

## 一、版本说明

### 1. 静态版

文件：

```text
/index.html
```

适合：

- GitHub Pages 在线预览
- 本地直接打开
- 单文件使用
- 临时填写合同
- JSON 导入 / 导出
- 打印 / 保存 PDF

特点：

- 单文件
- 无服务器
- 无数据库
- LocalStorage 自动保存
- 可直接双击打开
- 可直接用于 GitHub Pages

---

### 2. SQLite 持久化版

目录：

```text
/sqlite/
```

适合：

- 部署到 PHP 服务器
- 在合同沟通期间持续保存当前合同
- 关闭页面后重新打开继续填写
- 不需要反复导入 JSON
- 最终直接预览、打印和保存 PDF

特点：

- PHP + PDO + SQLite
- 只保存当前这一份合同
- 页面打开自动读取服务器数据
- 停止输入后自动同步 SQLite
- 可手动点击“保存”
- LocalStorage 继续作为临时保护
- JSON 导入 / 导出继续保留作为备用
- 不保存多份历史合同

> SQLite 版不是合同管理系统，只解决当前合同的持续保存问题。

---

## 二、项目定位

本项目主要用于网站建设项目签订前后的需求确认和合同打印。

适用于：

- WordPress 网站建设
- 企业官网
- 外贸网站
- 产品展示网站
- 品牌官网
- 网站改版项目
- 个人或公司承接的网站建设项目

设计原则：

> **核心说清楚，合同不要面面俱到。**

合同正文采用：

```text
9 个核心条款 + 1 个项目附件
```

打印目标：

```text
A4 约 4～5 页
```

---

## 三、当前合同结构

正文：

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

附件：

```text
附件一：项目需求与报价确认单
```

---

## 四、核心能力

### 项目范围

附件一用于明确：

- 项目名称
- 网站域名
- 网站类型
- 建站系统
- 语言版本
- 参考网站
- 制作周期
- 修改次数
- 质保时间
- 合同金额
- 页面 / 栏目
- 功能清单
- 资料录入
- 第三方费用
- 特别约定

未列入附件的新增页面、功能、语言、数据迁移、专项 SEO、第三方接口等，不默认包含。

### 修改与需求变更

支持设置免费修改轮次。

超出原范围的需求，应先确认：

```text
变更内容
+
新增费用
+
工期影响
```

再实施。

### 验收与尾款

核心流程：

```text
开发完成
↓
测试 / 验收
↓
验收通过
↓
支付尾款
↓
正式上线
↓
完整交付
```

### 第三方服务边界

对 WordPress、Elementor、主题、插件、字体、图库、API、域名、主机等第三方服务进行责任区分。

### SEO 边界

基础 SEO 仅指技术层面的基础配置，不承诺：

- 搜索引擎收录时间
- 关键词排名
- 网站流量
- 询盘数量

### 售后与备份

支持填写：

- 免费技术质保时间
- 最终交付备份期限
- 免费维护边界

---

## 五、静态版使用

根目录：

```text
index.html
```

可直接：

- 双击本地打开
- 上传到 GitHub Pages
- 部署到普通静态空间

数据主要保存在：

```text
LocalStorage
```

同时支持：

- JSON 导出
- JSON 导入
- A4 打印
- 浏览器保存 PDF

---

## 六、SQLite 版使用

SQLite 部署版位于：

```text
/sqlite/
```

服务器部署时，直接上传该目录中的全部内容。

例如：

```text
sqlite/
    ↓
/tools/website-contract/
```

最终服务器目录：

```text
/tools/website-contract/
├─ index.php
├─ .htaccess
├─ api/
└─ database/
```

访问：

```text
https://example.com/tools/website-contract/
```

详细部署说明见：

```text
/sqlite/README.md
```

---

## 七、SQLite 保存模型

SQLite 版只保存当前唯一合同。

数据库只有一条记录：

```text
id = 1
```

数据关系：

```text
SQLite
└─ 当前合同主数据

LocalStorage
└─ 浏览器临时草稿保护

JSON
└─ 手动备用 / 迁移

PDF
└─ 最终签署档案
```

下一次新合同可以直接清空并覆盖当前数据。

项目不保存历史合同记录。

---

## 八、仓库结构

```text
website-contract-builder/
├─ index.html
├─ README.md
├─ VERSION
├─ .gitignore
│
└─ sqlite/
   ├─ index.php
   ├─ README.md
   ├─ .htaccess
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

---

## 九、GitHub Pages

建议：

```text
Branch: main
Folder: /root
```

GitHub Pages 会直接加载根目录：

```text
index.html
```

SQLite 目录不会影响静态预览。

因此仓库可以同时做到：

```text
GitHub
→ 直接预览静态版

服务器
→ 部署 SQLite 版
```

---

## 十、数据安全

合同中可能包含：

- 姓名
- 电话
- 邮箱
- 地址
- 身份证号
- 统一社会信用代码
- 合同金额

因此建议：

- SQLite 版部署在访问控制之后
- 不要把真实 SQLite 数据库提交到 GitHub
- 不要把真实合同 JSON 提交到公开仓库
- 正式合同完成后保存 PDF
- JSON 只作为备用文件

仓库 `.gitignore` 已默认忽略：

```text
/sqlite/database/*.sqlite
/sqlite/database/*.db
*.json
*.pdf
```

---

## 十一、技术栈

### 静态版

```text
HTML
CSS
Vanilla JavaScript
LocalStorage
```

### SQLite 版

```text
PHP
PDO
SQLite
HTML
CSS
Vanilla JavaScript
LocalStorage
```

不使用：

- Node.js
- npm
- React
- Vue
- Laravel
- MySQL
- 构建工具

---

## 十二、当前版本

```text
v1.1.0
```

### v1.0.0

完成首个正式合同生成器版本。

### v1.0.1

优化：

- 打印分页
- 合同间距
- 签署区布局
- 附件功能清单
- 打印阴影
- A4 / PDF 输出体验

### v1.1.0

新增 SQLite 持久化版本：

- 当前合同保存到 SQLite
- 页面打开自动恢复
- 手动保存
- 延迟自动保存
- 服务器保存状态提示
- LocalStorage 临时保护
- JSON 备用
- 数据库访问保护

同时重新整理仓库：

```text
根目录 index.html
= GitHub Pages / 静态预览

/sqlite/
= PHP + SQLite 部署版
```

---

## 十三、设计原则

### 简单

不为了功能数量增加复杂度。

### 聚焦

当前项目只解决：

```text
填写
保存
确认
打印
```

### 可维护

静态预览版与服务器部署版分离。

### 按需扩展

当前明确不开发：

- 合同列表
- 多合同历史
- 客户管理
- CRM
- 报表
- 多用户
- 在线签名

只有未来真实产生需求时再增加。

---

## 十四、免责声明

本项目提供的是网站建设合同模板与填写工具。

主要帮助双方明确：

- 项目范围
- 价格
- 工期
- 修改
- 验收
- 交付
- 售后

本项目不构成法律意见，也不能替代专业律师针对具体项目、合同主体、交易金额及适用法律所提供的法律服务。

对于金额较大、跨境、涉及复杂知识产权或其他高风险项目，建议正式签署前由专业律师审核。
