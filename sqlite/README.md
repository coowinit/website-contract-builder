# SQLite 持久化部署版

本目录是网站建设合同生成器的 **PHP + SQLite 持久化版本**。

它仍然只保存“当前这一份合同”，不提供合同列表、历史合同、客户管理、统计报表等功能。

## 用途

适合部署到服务器后，在合同沟通、修改、确认、签订和打印期间持续保存当前合同数据。

正常使用流程：

```text
打开页面
↓
自动读取 SQLite 中的当前合同
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

## 文件结构

```text
sqlite/
├─ index.php
├─ .htaccess
├─ api/
│  ├─ bootstrap.php
│  ├─ load.php
│  └─ save.php
└─ database/
   ├─ .htaccess
   └─ index.html
```

第一次运行时会自动创建：

```text
database/contract.sqlite
```

该文件属于运行时数据，不应提交到 GitHub。

## 服务器要求

- PHP 8.x 推荐
- PDO
- PDO_SQLite
- `database/` 目录可写

## 部署

将 `sqlite/` 目录中的全部内容上传到服务器目标目录，例如：

```text
/tools/website-contract/
```

上传后目录应类似：

```text
/tools/website-contract/
├─ index.php
├─ .htaccess
├─ api/
└─ database/
```

然后访问：

```text
https://example.com/tools/website-contract/
```

## 数据保存

SQLite 只保存一条记录：

```text
id = 1
```

不会新增历史合同。

当前合同数据整体以 JSON 形式存入 SQLite，因此后续增加普通表单字段时，通常不需要修改数据库表结构。

## LocalStorage

LocalStorage 仍然保留，用作浏览器临时草稿保护。

如果服务器临时不可用，当前修改不会立即丢失。

## JSON

JSON 导入 / 导出仍然保留，但主要作为：

- 手动备份
- 临时迁移
- 灾难恢复

日常使用不需要反复导入导出。

## 安全

合同可能包含姓名、电话、邮箱、地址、证件信息和合同金额。

因此：

- `database/` 已禁止 Web 直接访问
- 根目录 `.gitignore` 已忽略 SQLite 运行时数据库
- 建议部署在 Basic Auth 或其他访问控制之后

## 本版本明确不做

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

当前版本只解决：

> 当前合同的持续保存、修改与最终打印。


## v1.1.1

本版本同步更新当前合同模板：

- 新增“费用说明”字段
- 新增“后续维护方式”字段
- 支持“乙方持续维护 / 完整技术交付”两种模式
- 持续维护到期或提前终止后，可在费用结清后要求完整技术交付
- 明确完整技术交付后的维护、备份、安全和服务器责任边界
- 所有新增字段继续随整份 JSON 自动保存到 SQLite，无需修改数据库表结构
