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
        qs('#slotArea').innerHTML = '';
        const today = new Date();
        calendarView = { year: today.getFullYear(), month: today.getMonth() };
        renderCalendar();
        goToStep(3);
      });
    });
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ---- STEP 3: calendar + slots ----
  const MONTH_NAMES_MN = ['1-р сар','2-р сар','3-р сар','4-р сар','5-р сар','6-р сар','7-р сар','8-р сар','9-р сар','10-р сар','11-р сар','12-р сар'];
  const DOW_NAMES_MN = ['Ня','Да','Мя','Лх','Пү','Ба','Бя'];
  const MAX_MONTHS_AHEAD = 3;
  let calendarView = null;

  function toISODate(y, m, d) {
    return y + '-' + String(m + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
  }

  function renderCalendar() {
    const cal = qs('#calendarWidget');
    const { year, month } = calendarView;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayISO = toISODate(today.getFullYear(), today.getMonth(), today.getDate());

    const firstOfMonth = new Date(year, month, 1);
    const startOffset = firstOfMonth.getDay(); // 0=Sun
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const minView = new Date(today.getFullYear(), today.getMonth(), 1);
    const maxView = new Date(today.getFullYear(), today.getMonth() + MAX_MONTHS_AHEAD, 1);
    const viewDate = new Date(year, month, 1);
    const atMin = viewDate <= minView;
    const atMax = viewDate >= maxView;

    let html = '<div class="calendar-head">';
    html += '<button type="button" class="calendar-nav-btn" id="calPrev"' + (atMin ? ' disabled' : '') + '>‹</button>';
    html += '<span class="cal-title">' + MONTH_NAMES_MN[month] + ' ' + year + '</span>';
    html += '<button type="button" class="calendar-nav-btn" id="calNext"' + (atMax ? ' disabled' : '') + '>›</button>';
    html += '</div>';
    html += '<div class="calendar-dow">' + DOW_NAMES_MN.map((d) => '<span>' + d + '</span>').join('') + '</div>';
    html += '<div class="calendar-grid">';
    for (let i = 0; i < startOffset; i++) html += '<div class="cal-day cal-day-empty"></div>';
    for (let d = 1; d <= daysInMonth; d++) {
      const iso = toISODate(year, month, d);
      const isPast = iso < todayISO;
      const isToday = iso === todayISO;
      const isSelected = state.date === iso;
      html += '<button type="button" class="cal-day' + (isToday ? ' cal-day-today' : '') + (isSelected ? ' selected' : '') + '"'
        + ' data-date="' + iso + '"' + (isPast ? ' disabled' : '') + '>' + d + '</button>';
    }
    html += '</div>';
    cal.innerHTML = html;

    const prevBtn = qs('#calPrev');
    const nextBtn = qs('#calNext');
    if (prevBtn) prevBtn.addEventListener('click', () => { changeMonth(-1); });
    if (nextBtn) nextBtn.addEventListener('click', () => { changeMonth(1); });

    qsa('.cal-day[data-date]', cal).forEach((btn) => {
      btn.addEventListener('click', () => {
        qsa('.cal-day', cal).forEach((b) => b.classList.remove('selected'));
        btn.classList.add('selected');
        state.date = btn.dataset.date;
        state.slot = null;
        loadSlots();
      });
    });
  }

  function changeMonth(delta) {
    let { year, month } = calendarView;
    month += delta;
    if (month < 0) { month = 11; year -= 1; }
    if (month > 11) { month = 0; year += 1; }
    calendarView = { year, month };
    renderCalendar();
  }

  function loadSlots() {
    const area = qs('#slotArea');
    area.innerHTML = '<p class="muted">Ачааллаж байна…</p>';
    const url = 'ajax/get_slots.php?doctor_id=' + state.doctor.id + '&service_id=' + state.service.id + '&date=' + state.date;
    fetch(url).then((r) => r.json()).then((res) => {
      if (res.error) { area.innerHTML = '<p class="muted">' + res.error + '</p>'; return; }
      if (!res.slots.length) {
        area.innerHTML = '<p class="muted">' + state.date + ' өдөр чөлөөтэй цаг алга байна. Календарь дээрээс өөр огноо сонгоно уу.</p>';
        return;
      }
      area.innerHTML = '<p class="muted" style="margin-bottom:8px;">' + state.date + ' өдрийн чөлөөтэй цагууд:</p><div class="slot-grid">' + res.slots.map((s) => (
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
    const name = qs('#nameInput').value.trim();
    const phone = qs('#phoneInput').value.trim();
    const email = qs('#emailInput').value.trim();

    if (!name || !phone || !email) {
      showAlert('Нэр, утас, имэйлээ бөглөнө үү.', 'error');
      return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showAlert('Имэйл хаягаа зөв оруулна уу.', 'error');
      return;
    }

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
        name: name,
        phone: phone,
        email: email,
      }),
    }).then((r) => r.json().then((body) => ({ ok: r.ok, body }))).then(({ ok, body }) => {
      if (!ok) {
        showAlert(body.error || 'Алдаа гарлаа.', 'error');
        this.disabled = false;
        this.textContent = 'Захиалга баталгаажуулах';
        if (String(body.error || '').includes('захиалагдсан')) { loadSlots(); }
        return;
      }
      window.location.href = 'booking-success.php?id=' + body.appointment_id;
    }).catch(() => {
      showAlert('Сүлжээний алдаа гарлаа. Дахин оролдоно уу.', 'error');
      this.disabled = false;
      this.textContent = 'Захиалга баталгаажуулах';
    });
  });
})();
