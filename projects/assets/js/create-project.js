const ProjectForm = (() => {
    const selectPtype = document.getElementById('pType');
    const selectPClass = document.getElementById('pClass');
    const licTitle = document.getElementById('licitacionT');
    const licMain = document.getElementById('licitacion');
    const contTitle = document.getElementById('contactoT');
    const contMain = document.getElementById('contacto');
    const classInfo = document.getElementById('classInfo');

    const formatDate = (start,end) => {
        const opts = { month:'long', day:'numeric', hour:'numeric', minute:'numeric', timeZone:'UTC' };
        const fStart = new Date(start).toLocaleDateString('es-ES',opts).replace(/de (\d{4})$/,'del $1');
        const fEnd = new Date(end).toLocaleDateString('es-ES',opts).replace(/de (\d{4})$/,'del $1');
        return `${fStart} - ${fEnd}`;
    };

    const createActividadItem = (nombre, fi, ft, desc, area) => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.innerHTML = `
            <h6>${nombre}</h6>
            <p>${formatDate(fi,ft)}</p>
            <p>${desc}</p>
            <input type="hidden" name="actividades[nombre][]" value="${nombre}">
            <input type="hidden" name="actividades[fechaInicio][]" value="${fi}">
            <input type="hidden" name="actividades[fechaTermino][]" value="${ft}">
            <input type="hidden" name="actividades[descripcion][]" value="${desc}">
            <input type="hidden" name="actividades[area][]" value="${area}">
        `;
        return li;
    };

    const createContactItem = (cName,cEmail,cargo,cNumero) => {
        const div = document.createElement('div');
        div.className = 'contact-item list-group-item';
        div.innerHTML = `
            <strong>${cName}</strong> - ${cargo}<br>
            Email: ${cEmail} - Tel: ${cNumero}
            <input type="hidden" name="contacto[nombre][]" value="${cName}">
            <input type="hidden" name="contacto[email][]" value="${cEmail}">
            <input type="hidden" name="contacto[cargo][]" value="${cargo}">
            <input type="hidden" name="contacto[contacto][]" value="${cNumero}">
        `;
        return div;
    };

    const init = () => {
        selectPtype?.addEventListener('change',()=>{
            licMain.style.display = licTitle.style.display = (selectPtype.value==1?'':'none');
            contMain.style.display = contTitle.style.display = (selectPtype.value==2?'':'none');
        });
        selectPClass?.addEventListener('change',()=>classInfo.style.display=(selectPClass.value==1?'':'none'));

        document.getElementById('formActividad')?.addEventListener('submit', e=>{
            e.preventDefault();
            const f = e.target;
            const li = createActividadItem(f.nombreActividad.value,f.fechaInicio.value,f.fechaTermino.value,f.descripcionActividad.value,f.areaAct.value);
            document.getElementById('listadoActividades').appendChild(li);
            f.reset();
            bootstrap.Modal.getInstance(document.getElementById('actividadModal')).hide();
        });

        document.getElementById('formContacto')?.addEventListener('submit', e=>{
            e.preventDefault();
            const f = e.target;
            contMain.appendChild(createContactItem(f.cName.value,f.cEmail.value,f.cargo.value,f.cNumero.value));
            f.reset();
            bootstrap.Modal.getInstance(document.getElementById('contactoModal')).hide();
        });
    };

    return { init };
})();

document.addEventListener('DOMContentLoaded',()=>ProjectForm.init());
