document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  if (toggle && sidebar) toggle.addEventListener('click', () => sidebar.classList.toggle('sidebar-open'));

  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
      if (!window.confirm(form.dataset.confirm || 'Lanjutkan tindakan ini?')) event.preventDefault();
    });
  });

  const consult = document.getElementById('feeConsult');
  const medicine = document.getElementById('feeMedicine');
  const preview = document.getElementById('totalPreview');
  const formatRupiah = number => new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
  }).format(number || 0);
  const updateTotal = () => {
    if (consult && medicine && preview) {
      preview.textContent = formatRupiah(Number(consult.value) + Number(medicine.value));
    }
  };
  if (consult && medicine) {
    consult.addEventListener('input', updateTotal);
    medicine.addEventListener('input', updateTotal);
  }
});
