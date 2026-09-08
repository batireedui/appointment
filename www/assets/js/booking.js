(function () {
  const data = window.BOOKING_DATA;
  const state = { service: null, doctor: null, date: null, slot: null };

  const panels = { 1: qs('#panel-1'), 2: qs('#panel-2'), 3: qs('#panel-3'), 4: qs('#panel-4') };
  const steps = qsa('.flow-step');

  function qs(sel, root) { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

  function goToStep(n) {
    Object.keys(panels).forEach((k) => { panels[k].style.display = (Number(k) === n) ? '' : 'none'; });
    steps.forEach((s) => {
      const stepNum = Number(s.dataset.step);
      s.classList.remove('active', 'done');
      if (stepNum < n) s.classList.add('done');
      if (stepNum === n) s.classList.add('active');
    });
  }

  function showAlert(message, type) {
    const box = qs('#alertBox');
    box.innerHTML = '<div class="alert alert-' + (type || 'error') + '">' + message + '</div>';
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
  function clearAlert() { qs('#alertBox').innerHTML = ''; }

  function money(n) {
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '₮';
  }

  // ---- STEP 1: select service ----
  qsa('[data-select-service]').forEach((card) => {
    card.addEventListener('click', () => {
      qsa('[data-select-service]').forEach((c) => c.classList.remove('selected'));
      card.classList.add('selected');
      state.service = {
        id: Number(card.dataset.id),
        name: card.dataset.name,
        duration: Number(card.dataset.duration),
        price: Number(card.dataset.price),
      };
      state.doctor = null;
      renderDoctorGrid();
      goToStep(2);
    });
  });

  function renderDoctorGrid() {
    const grid = qs('#doctorGrid');
    const doctorIds = data.serviceToDoctors[state.service.id] || [];
    const doctors = data.doctors.filter((d) => doctorIds.includes(d.id));
    if (!doctors.length) {
      grid.innerHTML = '<p class="muted">Энэ үйлчилгээг үзүүлдэг эмч одоогоор алга байна.</p>';
      return;
    }
    grid.innerHTML = doctors.map((d) => (
      '<div class="card card-select" data-select-doctor data-id="' + d.id + '" data-name="' + escapeHtml(d.name) + '">' +
        '<div class="doctor-photo"></div>' +
        '<div class="meta">' + escapeHtml(d.specialty) + '</div>' +
        '<h3>' + escapeHtml(d.name) + '</h3>' +
      '</div>'
    )).join('');
    qsa('[data-select-doctor]', grid).forEach((card) => {
      card.addEventListener('click', () => {
        qsa('[data-select-doctor]', grid).forEach((c) => c.classList.remove('selected'));
        card.classList.add('selected');
        state.doctor = { id: Number(card.dataset.id), name: card.dataset.name };
        state.date = null;
        state.slot = null;
        const dateInput = qs('#dateInput');
        dateInput.value = '';
        dateInput.min = new Date().toISOString().slice(0, 10);
        qs('#slotArea').innerHTML = '';
        goToStep(3);
      });
    });
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ---- STEP 3: date + slots ----
  qs('#dateInput').addEventListener('change', function () {
    state.date = this.value;
    state.slot = null;
    if (!state.date) return;
    loadSlots();
  });

  function loadSlots() {
    const area = qs('#slotArea');
    area.innerHTML = '<p class="muted">Ачааллаж байна…</p>';
    const url = 'ajax/get_slots.php?doctor_id=' + state.doctor.id + '&service_id=' + state.service.id + '&date=' + state.date;
    fetch(url).then((r) => r.json()).then((res) => {
      if (res.error) { area.innerHTML = '<p class="muted">' + res.error + '</p>'; return; }
      if (!res.slots.length) {
        area.innerHTML = '<p class="muted">Энэ өдөр чөлөөтэй цаг алга байна. Өөр огноо сонгоно уу.</p>';
        return;
      }
      area.innerHTML = '<div class="slot-grid">' + res.slots.map((s) => (
        '<div class="slot-btn" data-start="' + s.start + '" data-end="' + s.end + '">' + s.start.slice(0, 5) + '</div>'
      )).join('') + '</div>';
      qsa('.slot-btn', area).forEach((btn) => {
        btn.addEventListener('click', () => {
          qsa('.slot-btn', area).forEach((b) => b.classList.remove('selected'));
          btn.classList.add('selected');
          state.slot = { start: btn.dataset.start, end: btn.dataset.end };
          fillConfirmPanel();
          goToStep(4);
        });
      });
    }).catch(() => { area.innerHTML = '<p class="muted">Алдаа гарлаа. Дахин оролдоно уу.</p>'; });
  }

  function fillConfirmPanel() {
    qs('#cfService').textContent = state.service.name + ' (' + state.service.duration + ' мин)';
    qs('#cfDoctor').textContent = state.doctor.name;
    qs('#cfDate').textContent = state.date;
    qs('#cfTime').textContent = state.slot.start.slice(0, 5) + ' - ' + state.slot.end.slice(0, 5);
    qs('#cfPrice').textContent = money(state.service.price);
  }

  // ---- back buttons ----
  qsa('[data-back]').forEach((btn) => {
    btn.addEventListener('click', () => { clearAlert(); goToStep(Number(btn.dataset.back)); });
  });

  // ---- preselect from query string ----
  if (data.preselectService) {
    const card = qs('[data-select-service][data-id="' + data.preselectService + '"]');
    if (card) card.click();
  }
  if (data.preselectDoctor && state.service) {
    setTimeout(() => {
      const card = qs('[data-select-doctor][data-id="' + data.preselectDoctor + '"]');
      if (card) card.click();
    }, 0);
  }

  // ---- confirm booking ----
  qs('#confirmBtn').addEventListener('click', function () {
    clearAlert();
    this.disabled = true;
    this.textContent = 'Илгээж байна…';
    fetch('ajax/book.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        doctor_id: state.doctor.id,
        service_id: state.service.id,
        date: state.date,
        start: state.slot.start,
        end: state.slot.end,
        note: qs('#noteInput').value,
      }),
    }).then((r) => r.json().then((body) => ({ ok: r.ok, body }))).then(({ ok, body }) => {
      if (!ok) {
        showAlert(body.error || 'Алдаа гарлаа.', 'error');
        this.disabled = false;
        this.textContent = 'Захиалга баталгаажуулах';
        if (String(body.error || '').includes('захиалагдсан')) { loadSlots(); }
        return;
      }
      window.location.href = 'my-appointments.php?booked=1';
    }).catch(() => {
      showAlert('Сүлжээний алдаа гарлаа. Дахин оролдоно уу.', 'error');
      this.disabled = false;
      this.textContent = 'Захиалга баталгаажуулах';
    });
  });
})();
