<?php
$assetBase = is_dir(__DIR__ . '/assets') ? 'assets' : '../assets';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>网站建设合同生成器</title>
<link rel="stylesheet" href="<?= htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8') ?>/css/contract.css?v=1.2.0-20260929">
</head>
<body data-contract-mode="sqlite" data-storage-key="website_contract_builder_sqlite_v1" data-schema-version="2" data-api-load="api/load.php" data-api-save="api/save.php">
<div class="app" id="app">
  <div class="toolbar no-print">
    <div class="toolbar-title">
      <h1>网站建设合同生成器</h1>
      <span class="save-state" id="saveState">正在读取服务器数据…</span>
    </div>
    <div class="toolbar-actions">
      <button type="button" id="saveServer" class="primary edit-only">保存</button>
      <button type="button" id="togglePreview">预览模式</button>
      <button type="button" id="printBtn">打印 / 保存 PDF</button>
      <button type="button" id="newContract" class="edit-only">清空当前合同</button>
      <button type="button" id="exportJson" class="edit-only">导出 JSON</button>
      <label class="file-btn edit-only">导入 JSON<input type="file" id="importJson" accept=".json,application/json"></label>
    </div>
  </div>

  <div class="screen-tip no-print">当前合同以 SQLite 保存到服务器，浏览器 LocalStorage 作为临时保护；JSON 导出仅作为备用。</div>

  <main class="paper" id="contract">
    <h1 class="doc-title">网站建设合同</h1>

    <div class="contract-meta">
      <div class="meta-item"><span class="label">合同编号：</span><input class="input-line" data-key="meta.contractNo" type="text"></div>
      <div class="meta-item"><span class="label">签订日期：</span><input class="input-line" data-key="meta.signDate" type="date"></div>
      <div class="meta-item full-row"><span class="label">项目名称：</span><input class="input-line" data-key="project.name" type="text"></div>
    </div>

    <div class="party-grid">
      <section class="party-card">
        <h3>甲方（委托方）</h3>
        <div class="form-row">
          <label>主体类型</label>
          <select data-key="partyA.type">
            <option value="">请选择</option>
            <option>个人</option>
            <option>公司</option>
            <option>其他组织</option>
          </select>
        </div>
        <div class="form-row"><label>名称 / 姓名</label><input data-key="partyA.name" type="text"></div>
        <div class="form-row"><label>证件 / 信用代码</label><input data-key="partyA.idNo" type="text" placeholder="身份证号或统一社会信用代码"></div>
        <div class="form-row"><label>联系人 / 负责人</label><input data-key="partyA.contact" type="text"></div>
        <div class="form-row"><label>联系电话</label><input data-key="partyA.phone" type="tel"></div>
        <div class="form-row"><label>微信 / 其他联系方式</label><input data-key="partyA.wechat" type="text"></div>
        <div class="form-row"><label>邮箱</label><input data-key="partyA.email" type="email"></div>
        <div class="form-row"><label>地址</label><input data-key="partyA.address" type="text"></div>
      </section>
      <section class="party-card">
        <h3>乙方（承接方）</h3>
        <div class="form-row">
          <label>主体类型</label>
          <select data-key="partyB.type">
            <option value="">请选择</option>
            <option>个人</option>
            <option>公司</option>
            <option>其他组织</option>
          </select>
        </div>
        <div class="form-row"><label>名称 / 姓名</label><input data-key="partyB.name" type="text"></div>
        <div class="form-row"><label>证件 / 信用代码</label><input data-key="partyB.idNo" type="text" placeholder="身份证号或统一社会信用代码"></div>
        <div class="form-row"><label>联系人 / 负责人</label><input data-key="partyB.contact" type="text"></div>
        <div class="form-row"><label>联系电话</label><input data-key="partyB.phone" type="tel"></div>
        <div class="form-row"><label>微信 / 其他联系方式</label><input data-key="partyB.wechat" type="text"></div>
        <div class="form-row"><label>邮箱</label><input data-key="partyB.email" type="email"></div>
        <div class="form-row"><label>地址</label><input data-key="partyB.address" type="text"></div>
      </section>
    </div>

    <p class="intro">鉴于甲方委托乙方提供网站设计、开发、部署及相关技术服务，为明确双方权利义务，双方在平等、自愿、诚实信用的基础上，就本项目达成如下协议。</p>

    <p class="note" style="margin:-6px 0 16px;">双方确认：合同首部所填写的“联系人 / 负责人”为本项目指定联系人。指定联系人通过微信、邮件或双方实际使用的项目沟通工具作出的需求、设计、修改、验收等确认，视为对应一方的项目确认；联系人变更应及时书面通知对方。</p>

    <section class="clause">
      <h2>第一条　项目内容与范围</h2>
      <ol>
        <li>甲方委托乙方建设网站，具体页面、功能、语言、参考网站、域名、服务器、资料录入及其他服务，以《附件一：项目需求与报价确认单》为准。</li>
        <li>附件一未列明的新增页面、功能、语言版本、数据迁移、内容录入、专项 SEO、第三方接口等，不视为合同默认包含内容。</li>
        <li>参考网站仅用于风格、结构或功能方向参考，不代表完全复制；涉及第三方版权、商标、代码或其他知识产权的内容不得直接照搬。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第二条　合同金额与付款</h2>
      <div class="amount-box">
        <div class="amount-grid">
          <div class="inline-field"><span class="label">合同总金额：</span><span>￥</span><input data-key="payment.total" id="totalAmount" type="number" min="0" step="0.01"></div>
          <div class="inline-field"><span class="label">首付款比例：</span><input data-key="payment.depositRate" id="depositRate" type="number" min="0" max="100" step="1" value="50"><span>%</span></div>
          <div class="inline-field"><span class="label">首付款：</span><span>￥</span><input data-key="payment.deposit" id="depositAmount" type="text" readonly></div>
          <div class="inline-field"><span class="label">尾款：</span><span>￥</span><input data-key="payment.balance" id="balanceAmount" type="text" readonly></div>
        </div>
        <div class="inline-field" style="margin-top:8px">
          <span class="label">人民币大写：</span>
          <input data-key="payment.uppercase" id="amountUpperInput" type="text" placeholder="例如：陆仟元整">
        </div>
        <div class="form-row amount-note" style="margin-top:8px">
          <label>费用说明：</label>
          <textarea data-key="payment.notes" rows="2" placeholder="例如：本合同总价包含前三年域名、主机及基础技术维护费用；自第4年起续费为1800元/年。第三方服务价格如有调整，按实际续费价格执行。"></textarea>
        </div>
      </div>
      <ol>
        <li>甲方在合同生效后支付合同总额的 <input class="input-line editable-number" data-key="payment.depositRateText" id="depositRateText" type="number" min="0" max="100" value="50">% 作为首付款；如附件一约定其他付款节点，以附件一为准。</li>
        <li>项目验收通过（含本合同约定的视为验收通过）后 <input class="input-line editable-number" data-key="terms.balancePayDays" type="number" min="1" value="3"> 个工作日内，甲方应结清尾款。尾款结清后，乙方进行正式上线，并按照第八条及附件一约定的后续维护或交付方式执行。</li>
        <li>域名、主机、SSL、付费主题/插件、API、字体、图库等第三方费用，如未计入合同总价，由甲方另行承担。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第三条　项目周期</h2>
      <ol>
        <li>项目预计制作周期为 <input class="input-line editable-number" data-key="project.workDays" type="number" min="1"> 个工作日，自首付款到账且甲方提供项目所需首批完整资料之日起计算。</li>
        <li>等待甲方提供/补充资料、确认设计、提交修改意见，以及第三方平台审核、域名解析等非乙方可控时间，不计入制作周期。</li>
        <li>如甲方原因导致项目连续暂停超过 <input class="input-line editable-number" data-key="terms.longPauseDays" type="number" min="1" value="60"> 个自然日，双方应重新确认工期及未完成工作；协商不成的，乙方可书面解除合同，并按已完成工作量结算。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第四条　双方责任</h2>
      <ol>
        <li>甲方应及时提供真实、合法并拥有相应使用权的文字、图片、视频、商标、资质及其他资料，并对网站内容的合法性、广告宣传及知识产权承担责任。</li>
        <li>甲方应由指定联系人集中反馈需求、确认方案并按约付款；甲方内部意见不一致或非指定人员提出意见造成的返工，不属于乙方免费修改义务。</li>
        <li>乙方应按照本合同及附件一完成网站建设，并对项目过程中接触到的甲方未公开资料及账号信息承担合理保密义务。</li>
        <li>甲方或第三方自行修改程序、数据库、服务器配置、主题/插件等导致的故障或数据损失，由甲方自行承担；乙方协助处理可另行收费。</li>
        <li>乙方有权拒绝明显违法、侵权或未经双方确认的超范围需求。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第五条　修改与需求变更</h2>
      <ol>
        <li>合同总价默认包含 <input class="input-line editable-number" data-key="project.revisionRounds" type="number" min="0" value="2"> 轮集中修改；一轮修改指甲方针对同一阶段成果一次性汇总提出的修改意见。</li>
        <li>页面或方案一经甲方确认，再要求重新设计、更换整体风格、增加页面/功能/语言、改变架构、大量增加内容录入或数据迁移等，属于新增或重大变更。</li>
        <li>重大变更实施前，乙方应说明变更内容、新增费用及工期影响；经甲方指定联系人以微信、邮件或其他可留痕方式明确确认后实施。未明确确认前，乙方无义务执行，也不因此承担延期责任。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第六条　验收与上线</h2>
      <ol>
        <li>设计方案一经甲方确认，不得仅以主观审美变化为由要求免费重新设计。</li>
        <li>乙方完成附件一约定内容后，应通过双方约定的沟通方式通知甲方验收。甲方应在 <input class="input-line editable-number" data-key="terms.firstAcceptanceDays" type="number" min="1" value="5"> 个工作日内完成测试并一次性提出合同范围内的修改意见。</li>
        <li>乙方完成合理修改并再次通知后，甲方应在 <input class="input-line editable-number" data-key="terms.secondAcceptanceDays" type="number" min="1" value="3"> 个工作日内确认。在交付成果基本符合附件一约定的前提下，甲方逾期未提出明确书面异议，或已绑定正式域名对外使用、开展业务、投放广告、提交搜索引擎、接收真实询盘等，视为验收通过。</li>
        <li>验收通过后，按第二条约定结清尾款；尾款未结清前，乙方可仅提供测试预览或受限访问，不承担正式上线及完整交付义务。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第七条　第三方服务与知识产权</h2>
      <ol>
        <li>甲方提供的商标、文字、图片、视频及其他资料，其相关权利归甲方或原权利人所有；因甲方提供资料引发的侵权纠纷，由甲方承担责任。</li>
        <li>WordPress、Elementor、第三方主题、插件、字体、图库、API 等权利归各自权利人所有，并受其授权规则约束。第三方涨价、停服、接口变化、停止维护或兼容性变化，不属于乙方违约。</li>
        <li>乙方在项目开始前已拥有或独立积累的通用代码、组件、工具、开发框架及可复用技术，不因本合同而转让所有权；甲方有权正常运行、维护和迁移已交付的网站。</li>
        <li>如附件一包含“基础 SEO”，仅指网站技术层面的基础配置，不保证搜索引擎收录时间、关键词排名、访问量或询盘数量。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第八条　交付与售后</h2>
      <ol>
        <li>本项目后续处理方式分为“乙方持续维护”和“完整技术交付”两种，以附件一所选方式为准。</li>
        <li>选择“乙方持续维护”时，甲方可正常使用网站后台进行日常内容管理，乙方在约定服务期内负责程序、主题/插件、数据库、服务器环境、必要的数据备份与恢复及基础安全等技术维护；具体服务期限、续费标准及第三方费用以本合同“费用说明”或附件一约定为准。</li>
        <li>持续维护服务期满，或甲方提前终止维护合作并要求完整技术交付时，在甲方结清全部应付款项及已发生的第三方费用后，乙方应移交网站文件/源码、数据库备份、后台管理员账号，以及双方约定可转移的域名、服务器或第三方账号权限。完整技术交付完成后，除双方另有维护约定外，乙方不再承担后续免费升级、功能优化、数据备份与恢复、网站安全或服务器环境维护义务。</li>
        <li>如附件一直接选择“完整技术交付”，乙方在甲方结清全部应付款项后按前款约定完成移交。完整交付后，甲方或第三方自行修改程序、数据库、服务器配置、主题/插件等造成的故障、兼容性问题、数据损失或安全问题，由甲方自行承担；乙方后续协助可另行收费。</li>
        <li>自验收通过之日起，乙方提供 <input class="input-line editable-number" data-key="project.warrantyDays" type="number" min="0" value="30"> 天免费技术质保，仅处理本合同范围内由乙方制作内容本身存在的程序性故障；免费质保不等同于持续维护服务。</li>
        <li>选择“乙方持续维护”时，由乙方按约定及实际服务器条件进行必要备份；完成“完整技术交付”后，日常备份责任转由甲方承担。乙方可免费保留最终交付版本备份 <input class="input-line editable-number" data-key="terms.backupDays" type="number" min="0" value="30"> 天，该备份不构成持续备份或恢复义务；期限届满后乙方无继续保存义务。</li>
      </ol>
    </section>

    <section class="clause">
      <h2>第九条　违约、通知与争议</h2>
      <ol>
        <li>甲方逾期付款超过 <input class="input-line editable-number" data-key="terms.overdueDays" type="number" min="1" value="7"> 个自然日，乙方有权暂停开发、上线、维护或交付，暂停期间不计入乙方工期。</li>
        <li>任何一方严重违约，经另一方书面催告后仍未在合理期限内改正的，守约方有权暂停履行或解除合同；项目解除时按已完成工作量及已发生的第三方成本结算。</li>
        <li>双方确认微信、邮件及双方实际使用的项目沟通工具可用于需求、设计、变更、验收、付款等通知和确认，相关电子记录可作为履约依据。</li>
        <li>因不可抗力或非双方可控的第三方原因导致不能或延迟履行的，双方根据实际影响协商顺延、部分履行或解除。</li>
        <li>因本合同产生争议，双方应先友好协商；协商不成的，依法向有管辖权的人民法院提起诉讼。</li>
        <li>本合同附件及双方确认的补充协议、变更记录均为本合同组成部分，与正文具有同等效力。本合同自双方签字或盖章之日起生效。</li>
      </ol>
    </section>

    <div class="signature">
      <div class="sign-box">
        <h3>甲方（签字/盖章）</h3>
        <div class="sign-line"><span>签署人 / 授权代表：</span><input class="input-line" data-key="sign.partyARep" type="text"></div>
        <div class="sign-line"><span>联系电话：</span><input class="input-line" data-key="sign.partyAPhone" type="text"></div>
        <div class="sign-line"><span>签署日期：</span><input class="input-line" data-key="sign.partyADate" type="date"></div>
      </div>
      <div class="sign-box">
        <h3>乙方（签字/盖章）</h3>
        <div class="sign-line"><span>签署人 / 授权代表：</span><input class="input-line" data-key="sign.partyBRep" type="text"></div>
        <div class="sign-line"><span>联系电话：</span><input class="input-line" data-key="sign.partyBPhone" type="text"></div>
        <div class="sign-line"><span>签署日期：</span><input class="input-line" data-key="sign.partyBDate" type="date"></div>
      </div>
    </div>

    <section class="attachment">
      <h2 class="attachment-title">附件一：项目需求与报价确认单</h2>

      <table class="project-info">
        <tr><th>项目名称</th><td><input data-key="project.name2" type="text"></td></tr>
        <tr><th>网站域名</th><td><input data-key="project.domain" type="text" placeholder="如暂无可留空"></td></tr>
        <tr><th>网站类型 / 建站系统</th><td><input data-key="project.siteType" type="text" placeholder="例如：外贸企业官网 / WordPress + Elementor"></td></tr>
        <tr><th>语言版本</th><td><input data-key="project.languages" type="text" placeholder="例如：中文、英文"></td></tr>
        <tr><th>参考网站</th><td><textarea data-key="project.references" rows="2"></textarea></td></tr>
        <tr><th>制作周期 / 修改 / 质保</th><td>
          <input data-key="project.workDays2" type="number" min="1" style="width:80px"> 个工作日；
          免费修改 <input data-key="project.revisionRounds2" type="number" min="0" style="width:70px"> 轮；
          质保 <input data-key="project.warrantyDays2" type="number" min="0" style="width:70px"> 天
        </td></tr>
        <tr><th>合同总价 / 付款</th><td>
          ￥ <input data-key="payment.total2" type="number" min="0" step="0.01" style="width:150px"> 元；
          <input data-key="payment.method" type="text" placeholder="例如：50%首款 + 50%尾款" style="width:55%">
        </td></tr>
        <tr><th>后续维护方式</th><td>
          <select data-key="maintenance.mode" style="max-width:260px">
            <option value="乙方持续维护">乙方持续维护</option>
            <option value="完整技术交付">完整技术交付</option>
          </select>
          <span class="field-help">持续维护到期或提前终止后，费用结清即可要求完整技术交付。</span>
        </td></tr>
      </table>

      <h3 class="section-title">页面 / 栏目清单</h3>
      <table class="page-table">
        <thead>
          <tr><th>序号</th><th>页面 / 栏目</th><th>主要内容 / 功能</th><th>备注</th></tr>
        </thead>
        <tbody id="pageRows"></tbody>
      </table>
      <div class="no-print edit-only" style="margin-top:8px">
        <button type="button" id="addRow">+ 增加一行</button>
        <button type="button" id="removeRow">- 删除最后一行</button>
      </div>

      <h3 class="section-title">功能清单</h3>
      <div class="checks" id="featureChecks">
        <label class="check"><input type="checkbox" data-key="features.responsive">响应式适配</label>
        <label class="check"><input type="checkbox" data-key="features.products">产品展示</label>
        <label class="check"><input type="checkbox" data-key="features.blog">新闻 / 博客</label>
        <label class="check"><input type="checkbox" data-key="features.cases">案例展示</label>
        <label class="check"><input type="checkbox" data-key="features.inquiry">留言 / 询盘</label>
        <label class="check"><input type="checkbox" data-key="features.search">站内搜索</label>
        <label class="check"><input type="checkbox" data-key="features.multilingual">多语言</label>
        <label class="check"><input type="checkbox" data-key="features.seo">基础 SEO</label>
        <label class="check"><input type="checkbox" data-key="features.analytics">流量统计</label>
      </div>
      <div class="form-row"><label>其他功能</label><input data-key="features.other" type="text" placeholder="如有，请详细说明；未填写视为无"></div>

      <h3 class="section-title">特别约定</h3>
      <textarea data-key="special.notes" rows="3" placeholder="可填写：资料由谁提供、首批录入数量、特殊第三方费用、额外交付要求及其他本项目特有约定"></textarea>
      <div class="special-notes-print" id="specialNotesPrint"></div>

      <p class="note" style="margin:10px 0 0;">说明：基础 SEO 仅指网站技术层面的基础配置，不保证搜索引擎收录时间、关键词排名、访问量或询盘数量。</p>

      <p style="margin-top:10px"><strong>双方确认：</strong>以上项目需求、范围、价格及周期已阅读并确认。</p>

      <div class="attachment-sign">
        <div>
          <div class="sign-line"><span>甲方确认：</span><input class="input-line" data-key="attachment.partyAConfirm" type="text"></div>
          <div class="sign-line"><span>日期：</span><input class="input-line" data-key="attachment.partyADate" type="date"></div>
        </div>
        <div>
          <div class="sign-line"><span>乙方确认：</span><input class="input-line" data-key="attachment.partyBConfirm" type="text"></div>
          <div class="sign-line"><span>日期：</span><input class="input-line" data-key="attachment.partyBDate" type="date"></div>
        </div>
      </div>
    </section>
  </main>
</div>

<script src="<?= htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8') ?>/js/contract.js?v=1.2.0-20260929"></script>
</body>
</html>