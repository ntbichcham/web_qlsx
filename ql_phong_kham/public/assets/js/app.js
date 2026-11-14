document.addEventListener('DOMContentLoaded', () => {
  const roleSelect = document.getElementById('roleSelect');
  const profileGroup = document.getElementById('profileGroup');
  const specialtyGroup = document.getElementById('specialtyGroup');
  const positionGroup = document.getElementById('positionGroup');
  const introductionGroup = document.getElementById('introductionGroup');
  const adminNote = document.getElementById('adminNote');
  const toggleRoleFields = () => {
    if (!roleSelect) return;
    const role = roleSelect.value;
    const isDoctor = role === 'BAC_SI';
    const isStaff = role === 'NHAN_VIEN';
    const isAdmin = role === 'QUAN_TRI_VIEN';

    profileGroup?.classList.toggle('d-none', isAdmin);
    specialtyGroup?.classList.toggle('d-none', !isDoctor);
    introductionGroup?.classList.toggle('d-none', !isDoctor);
    positionGroup?.classList.toggle('d-none', !isStaff);
    adminNote?.classList.toggle('d-none', !isAdmin);

    // Không gửi lên các trường không thuộc vai trò đang chọn.
    profileGroup?.querySelectorAll('input').forEach((el) => { el.disabled = isAdmin; });
    specialtyGroup?.querySelectorAll('input').forEach((el) => { el.disabled = !isDoctor; });
    introductionGroup?.querySelectorAll('textarea').forEach((el) => { el.disabled = !isDoctor; });
    positionGroup?.querySelectorAll('input').forEach((el) => { el.disabled = !isStaff; });
  };
  roleSelect?.addEventListener('change', toggleRoleFields);
  toggleRoleFields();

  const scheduleSelect = document.getElementById('scheduleSelect');
  const dateInput = document.getElementById('appointmentDate');
  const syncScheduleDate = () => {
    const option = scheduleSelect?.selectedOptions?.[0];
    if (option?.dataset?.date && dateInput) dateInput.value = option.dataset.date;
  };
  scheduleSelect?.addEventListener('change', syncScheduleDate);
  syncScheduleDate();
});
