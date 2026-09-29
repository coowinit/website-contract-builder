(function(){
  'use strict';

  const body = document.body;
  const MODE = body.dataset.contractMode || 'static';
  const STORAGE_KEY = body.dataset.storageKey || 'website_contract_builder_v1';
  const SCHEMA_VERSION = Number(body.dataset.schemaVersion || 1);
  const API_LOAD = body.dataset.apiLoad || 'api/load.php';
  const API_SAVE = body.dataset.apiSave || 'api/save.php';
  const IS_SQLITE = MODE === 'sqlite';

  const app = document.getElementById('app');
  const saveState = document.getElementById('saveState');
  const pageRows = document.getElementById('pageRows');

  let localSaveTimer = null;
  let serverSaveTimer = null;
  let isLoading = IS_SQLITE;
  let lastServerPayload = '';

  function today(){
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${day}`;
  }

  function defaultContractNo(){
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `WEB-${y}-${m}${day}-01`;
  }

  function setDeep(obj, path, value){
    const parts = path.split('.');
    let cur = obj;
    parts.forEach((p,i)=>{
      if(i===parts.length-1){
        cur[p]=value;
      }else{
        if(!cur[p] || typeof cur[p] !== 'object') cur[p]={};
        cur=cur[p];
      }
    });
  }

  function getDeep(obj,path){
    return path.split('.').reduce((o,k)=>o && o[k]!==undefined ? o[k] : undefined,obj);
  }

  function escapeHtml(s){
    return String(s).replace(/[&<>"']/g, m=>({
      '&':'&amp;',
      '<':'&lt;',
      '>':'&gt;',
      '"':'&quot;',
      "'":'&#039;'
    }[m]));
  }

  function createPageRows(n=8){
    pageRows.innerHTML='';
    for(let i=0;i<n;i++) addPageRow();
  }

  function addPageRow(data={}){
    const index = pageRows.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="text-align:center">${index+1}</td>
      <td><input type="text" data-row-key="name" value="${escapeHtml(data.name||'')}"></td>
      <td><input type="text" data-row-key="function" value="${escapeHtml(data.function||'')}"></td>
      <td><input type="text" data-row-key="note" value="${escapeHtml(data.note||'')}"></td>`;
    pageRows.appendChild(tr);
    tr.querySelectorAll('input').forEach(el=>el.addEventListener('input', scheduleSave));
  }

  function getRows(){
    return [...pageRows.querySelectorAll('tr')].map(tr=>{
      const cells={};
      tr.querySelectorAll('[data-row-key]').forEach(el=>cells[el.dataset.rowKey]=el.value);
      return cells;
    });
  }

  function serialize(){
    const data={
      version:SCHEMA_VERSION,
      savedAt:new Date().toISOString(),
      pageRows:getRows()
    };
    document.querySelectorAll('[data-key]').forEach(el=>{
      const value=el.type==='checkbox' ? el.checked : el.value;
      setDeep(data,el.dataset.key,value);
    });
    return data;
  }

  function applyData(data){
    document.querySelectorAll('[data-key]').forEach(el=>{
      const v=getDeep(data,el.dataset.key);
      if(v===undefined || v===null) return;
      if(el.type==='checkbox') el.checked=!!v;
      else el.value=v;
    });

    if(Array.isArray(data.pageRows)){
      pageRows.innerHTML='';
      data.pageRows.forEach(r=>addPageRow(r));
      if(data.pageRows.length===0) createPageRows(8);
    }

    syncLinkedFields();
    updateAmounts();
    autoGrowAll();
    syncSpecialNotesPrint();
  }

  function saveLocalNow(){
    try{
      localStorage.setItem(STORAGE_KEY,JSON.stringify(serialize()));
    }catch(e){}
  }

  function saveStaticNow(){
    saveLocalNow();
    const time=new Date().toLocaleTimeString('zh-CN',{hour:'2-digit',minute:'2-digit'});
    saveState.textContent=`已自动保存 ${time}`;
  }

  function loadStatic(){
    const raw=localStorage.getItem(STORAGE_KEY);
    if(!raw) return false;
    try{
      applyData(JSON.parse(raw));
      return true;
    }catch(e){
      return false;
    }
  }

  function scheduleSave(){
    if(IS_SQLITE){
      if(isLoading) return;
      clearTimeout(localSaveTimer);
      clearTimeout(serverSaveTimer);
      saveState.textContent='有未保存修改';
      localSaveTimer=setTimeout(saveLocalNow,180);
      serverSaveTimer=setTimeout(()=>saveServer(false),1800);
      return;
    }

    clearTimeout(localSaveTimer);
    saveState.textContent='正在保存…';
    localSaveTimer=setTimeout(saveStaticNow,250);
  }

  function formatServerTime(iso){
    if(!iso) return '';
    const d=new Date(iso);
    if(Number.isNaN(d.getTime())) return '';
    return d.toLocaleTimeString('zh-CN',{hour:'2-digit',minute:'2-digit'});
  }

  async function saveServer(manual=true){
    if(!IS_SQLITE){
      saveStaticNow();
      return true;
    }

    clearTimeout(serverSaveTimer);
    clearTimeout(localSaveTimer);

    const data=serialize();
    const payload=JSON.stringify(data);
    saveLocalNow();

    if(!manual && payload===lastServerPayload){
      saveState.textContent='已保存';
      return true;
    }

    saveState.textContent='正在保存…';

    try{
      const response=await fetch(API_SAVE,{
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json'},
        credentials:'same-origin',
        cache:'no-store',
        body:payload
      });
      const result=await response.json().catch(()=>({}));
      if(!response.ok || !result.success){
        throw new Error(result.message || `HTTP ${response.status}`);
      }

      lastServerPayload=payload;
      const time=formatServerTime(result.updated_at) || new Date().toLocaleTimeString('zh-CN',{hour:'2-digit',minute:'2-digit'});
      saveState.textContent=`服务器已保存 ${time}`;
      return true;
    }catch(err){
      saveState.textContent='服务器保存失败，本地草稿已保留';
      if(manual){
        alert('服务器保存失败。当前修改仍已保存在本浏览器中，请检查 PHP / SQLite 环境后重试。\n\n'+err.message);
      }
      return false;
    }
  }

  async function loadInitialSqliteData(){
    let localData=null;
    const raw=localStorage.getItem(STORAGE_KEY);
    if(raw){
      try{ localData=JSON.parse(raw); }catch(e){}
    }

    try{
      const response=await fetch(API_LOAD,{
        method:'GET',
        headers:{'Accept':'application/json'},
        credentials:'same-origin',
        cache:'no-store'
      });
      const result=await response.json().catch(()=>({}));
      if(!response.ok || !result.success){
        throw new Error(result.message || `HTTP ${response.status}`);
      }

      if(result.data && typeof result.data==='object'){
        applyData(result.data);
        lastServerPayload=JSON.stringify(serialize());
        saveLocalNow();
        const time=formatServerTime(result.updated_at);
        saveState.textContent=time ? `服务器已保存 ${time}` : '已从服务器恢复';
      }else if(localData){
        applyData(localData);
        saveState.textContent='正在将本地草稿保存到服务器…';
        await saveServer(true);
      }else{
        setDefaults();
        saveLocalNow();
        await saveServer(false);
      }
    }catch(err){
      if(localData){
        applyData(localData);
        saveState.textContent='服务器不可用，已恢复本地草稿';
      }else{
        setDefaults();
        saveLocalNow();
        saveState.textContent='服务器不可用，当前使用本地草稿';
      }
    }finally{
      isLoading=false;
      updateAmounts();
      autoGrowAll();
      syncSpecialNotesPrint();
    }
  }

  function updateAmounts(){
    const total=Number(document.getElementById('totalAmount').value||0);
    const rate=Number(document.getElementById('depositRate').value||0);
    const deposit=total*rate/100;
    const balance=total-deposit;
    document.getElementById('depositAmount').value=total ? deposit.toFixed(2) : '';
    document.getElementById('balanceAmount').value=total ? balance.toFixed(2) : '';
    document.getElementById('depositRateText').value=rate || '';
  }

  function syncLinkedFields(source){
    const map=[
      ['project.name','project.name2'],
      ['project.workDays','project.workDays2'],
      ['project.revisionRounds','project.revisionRounds2'],
      ['project.warrantyDays','project.warrantyDays2'],
      ['payment.total','payment.total2'],
      ['meta.signDate','sign.partyADate'],
      ['meta.signDate','sign.partyBDate'],
      ['partyB.contact','sign.partyBRep'],
      ['partyB.phone','sign.partyBPhone'],
      ['partyA.phone','sign.partyAPhone'],
      ['partyA.contact','sign.partyARep']
    ];

    map.forEach(([a,b])=>{
      const ea=document.querySelector(`[data-key="${a}"]`);
      const eb=document.querySelector(`[data-key="${b}"]`);
      if(!ea || !eb) return;
      if(source===eb && eb.value!=='') return;
      if(source===ea || !eb.value) eb.value=ea.value;
    });

    const partyAName=document.querySelector('[data-key="partyA.name"]');
    const partyAContact=document.querySelector('[data-key="partyA.contact"]');
    const partyARep=document.querySelector('[data-key="sign.partyARep"]');
    if(partyARep && !partyARep.value && partyAName){
      partyARep.value=(partyAContact && partyAContact.value) ? partyAContact.value : partyAName.value;
    }

    const partyBName=document.querySelector('[data-key="partyB.name"]');
    const partyBContact=document.querySelector('[data-key="partyB.contact"]');
    const partyBRep=document.querySelector('[data-key="sign.partyBRep"]');
    if(partyBRep && !partyBRep.value && partyBName){
      partyBRep.value=(partyBContact && partyBContact.value) ? partyBContact.value : partyBName.value;
    }
  }

  function syncSpecialNotesPrint(){
    const source=document.querySelector('textarea[data-key="special.notes"]');
    const target=document.getElementById('specialNotesPrint');
    if(source && target) target.textContent=source.value || '';
  }

  const confirmDateKeys=['attachment.partyADate','attachment.partyBDate'];

  function prepareEmptyConfirmDatesForPrint(){
    confirmDateKeys.forEach(key=>{
      const el=document.querySelector(`[data-key="${key}"]`);
      if(!el || el.value) return;
      el.dataset.printOriginalType=el.type;
      el.type='text';
      el.value='';
      el.placeholder='';
    });
  }

  function restoreConfirmDatesAfterPrint(){
    confirmDateKeys.forEach(key=>{
      const el=document.querySelector(`[data-key="${key}"]`);
      if(!el || !el.dataset.printOriginalType) return;
      el.type=el.dataset.printOriginalType;
      delete el.dataset.printOriginalType;
    });
  }

  function syncFormStateForPrint(){
    document.querySelectorAll('textarea[data-key]').forEach(el=>{
      el.defaultValue=el.value;
      el.textContent=el.value;
      autoGrow(el);
    });

    document.querySelectorAll('input[data-key]').forEach(el=>{
      if(el.type==='checkbox'){
        el.defaultChecked=el.checked;
        if(el.checked) el.setAttribute('checked','checked');
        else el.removeAttribute('checked');
      }else{
        el.defaultValue=el.value;
        el.setAttribute('value',el.value);
      }
    });

    document.querySelectorAll('select[data-key]').forEach(el=>{
      [...el.options].forEach(option=>{
        const selected=option.value===el.value;
        option.defaultSelected=selected;
        if(selected) option.setAttribute('selected','selected');
        else option.removeAttribute('selected');
      });
    });

    syncSpecialNotesPrint();
  }

  function autoGrow(el){
    if(el.tagName!=='TEXTAREA') return;
    el.style.height='auto';
    el.style.height=Math.max(el.scrollHeight,64)+'px';
  }

  function autoGrowAll(){
    document.querySelectorAll('textarea').forEach(autoGrow);
  }

  function setDefaults(){
    const defaults={
      'meta.contractNo':defaultContractNo(),
      'meta.signDate':today(),
      'payment.depositRate':'50',
      'payment.depositRateText':'50',
      'terms.pauseDays':'15',
      'terms.longPauseDays':'60',
      'project.revisionRounds':'2',
      'project.revisionRounds2':'2',
      'terms.firstAcceptanceDays':'5',
      'terms.secondAcceptanceDays':'3',
      'project.warrantyDays':'30',
      'project.warrantyDays2':'30',
      'terms.overdueDays':'7',
      'terms.balancePayDays':'3',
      'terms.backupDays':'30',
      'payment.method':'50%首款 + 50%尾款',
      'maintenance.mode':'乙方持续维护'
    };

    Object.entries(defaults).forEach(([k,v])=>{
      const el=document.querySelector(`[data-key="${k}"]`);
      if(el && !el.value) el.value=v;
    });
    syncLinkedFields();
  }

  async function handlePrint(){
    if(IS_SQLITE){
      clearTimeout(localSaveTimer);
      clearTimeout(serverSaveTimer);
      saveLocalNow();
      await saveServer(false);
    }else{
      clearTimeout(localSaveTimer);
      saveStaticNow();
    }

    syncFormStateForPrint();
    autoGrowAll();
    prepareEmptyConfirmDatesForPrint();

    const wasPreview=app.classList.contains('preview-mode');
    app.classList.add('preview-mode');
    setTimeout(()=>{
      window.print();
      if(!wasPreview) app.classList.remove('preview-mode');
    },100);
  }

  async function handleImport(e){
    const file=e.target.files[0];
    if(!file) return;

    try{
      const data=JSON.parse(await file.text());
      applyData(data);
      if(IS_SQLITE){
        saveLocalNow();
        await saveServer(true);
        alert('合同数据已导入并保存到服务器。');
      }else{
        saveStaticNow();
        alert('合同数据已导入。');
      }
    }catch(err){
      alert('导入失败：请选择本工具导出的 JSON 文件。');
    }
    e.target.value='';
  }

  function handleExport(){
    const data=serialize();
    const no=(getDeep(data,'meta.contractNo')||'contract').replace(/[\\/:*?"<>|]/g,'-');
    const blob=new Blob([JSON.stringify(data,null,2)],{type:'application/json;charset=utf-8'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url;
    a.download=no+'.json';
    a.click();
    URL.revokeObjectURL(url);
  }

  async function handleNewContract(){
    if(IS_SQLITE){
      if(!confirm('确认清空当前合同？\n\n此操作会用空白合同覆盖服务器中的当前数据。建议如需留档，先导出 JSON 或保存 PDF。')) return;
      isLoading=true;
      localStorage.removeItem(STORAGE_KEY);
      document.querySelectorAll('[data-key]').forEach(el=>{
        if(el.type==='checkbox') el.checked=false;
        else el.value='';
      });
      createPageRows(8);
      setDefaults();
      updateAmounts();
      autoGrowAll();
      syncSpecialNotesPrint();
      isLoading=false;
      saveLocalNow();
      await saveServer(true);
      return;
    }

    if(!confirm('新建合同会清空当前客户和项目数据。建议先导出 JSON 备份。是否继续？')) return;
    localStorage.removeItem(STORAGE_KEY);
    location.reload();
  }

  function handleClearStatic(){
    if(!confirm('确认清空当前合同全部数据？此操作不可撤销。')) return;
    localStorage.removeItem(STORAGE_KEY);
    document.querySelectorAll('[data-key]').forEach(el=>{
      if(el.type==='checkbox') el.checked=false;
      else el.value='';
    });
    createPageRows(8);
    setDefaults();
    updateAmounts();
    autoGrowAll();
    syncSpecialNotesPrint();
    saveStaticNow();
  }

  document.addEventListener('input',e=>{
    if(!e.target.matches('[data-key]')) return;

    if(['totalAmount','depositRate'].includes(e.target.id)) updateAmounts();
    if(e.target.id==='depositRateText'){
      document.getElementById('depositRate').value=e.target.value;
      updateAmounts();
    }

    syncLinkedFields(e.target);
    autoGrow(e.target);
    if(e.target.matches('textarea[data-key="special.notes"]')) syncSpecialNotesPrint();
    scheduleSave();
  });

  document.addEventListener('change',e=>{
    if(e.target.matches('[data-key]')){
      syncLinkedFields(e.target);
      scheduleSave();
    }
  });

  window.addEventListener('afterprint',restoreConfirmDatesAfterPrint);

  const saveServerButton=document.getElementById('saveServer');
  if(saveServerButton) saveServerButton.addEventListener('click',()=>saveServer(true));

  const togglePreview=document.getElementById('togglePreview');
  if(togglePreview){
    togglePreview.addEventListener('click',function(){
      app.classList.toggle('preview-mode');
      this.textContent=app.classList.contains('preview-mode')?'返回编辑':'预览模式';
      window.scrollTo({top:0,behavior:'smooth'});
    });
  }

  const printButton=document.getElementById('printBtn');
  if(printButton) printButton.addEventListener('click',handlePrint);

  const exportButton=document.getElementById('exportJson');
  if(exportButton) exportButton.addEventListener('click',handleExport);

  const importInput=document.getElementById('importJson');
  if(importInput) importInput.addEventListener('change',handleImport);

  const newContractButton=document.getElementById('newContract');
  if(newContractButton) newContractButton.addEventListener('click',handleNewContract);

  const clearButton=document.getElementById('clearData');
  if(clearButton) clearButton.addEventListener('click',handleClearStatic);

  const addRowButton=document.getElementById('addRow');
  if(addRowButton) addRowButton.addEventListener('click',()=>{
    addPageRow();
    scheduleSave();
  });

  const removeRowButton=document.getElementById('removeRow');
  if(removeRowButton){
    removeRowButton.addEventListener('click',()=>{
      if(pageRows.children.length>1){
        pageRows.lastElementChild.remove();
        [...pageRows.children].forEach((tr,i)=>tr.children[0].textContent=i+1);
        scheduleSave();
      }
    });
  }

  createPageRows(8);

  if(IS_SQLITE){
    setDefaults();
    updateAmounts();
    autoGrowAll();
    syncSpecialNotesPrint();
    loadInitialSqliteData();
  }else{
    if(!loadStatic()){
      setDefaults();
      saveStaticNow();
    }
    updateAmounts();
    autoGrowAll();
    syncSpecialNotesPrint();
  }
})();
