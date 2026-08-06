document.addEventListener('DOMContentLoaded', function () {
    // Маска для телефона
    Inputmask("+7 (999)-999-99-99").mask(document.getElementById('phone'));
  
    // Открытие модального окна
    const modal = document.getElementById('bookingModal');
const openModalBtn = document.getElementById('openModal');
const closeModalBtn = modal.querySelector('.close');

openModalBtn.onclick = () => {
  modal.style.display = 'block';
};

closeModalBtn.onclick = () => {
  modal.style.display = 'none';
};

window.onclick = (event) => {
  if (event.target === modal) {
    modal.style.display = 'none';
  }
};

  
    const tariffSelect = document.getElementById('tariff');
    const additionalOptions = document.getElementById('additionalOptions');
  
    tariffSelect.addEventListener('change', function () {
      if (this.value === 'Открытая игра') {
        additionalOptions.style.display = 'block';
      } else {
        additionalOptions.style.display = 'none';
      }
    });
  
    document.getElementById('bookingForm').addEventListener('submit', function (event) {
      const name = document.getElementById('name').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const date = document.getElementById('date').value;
      const time = document.getElementById('time').value;
      const tariff = document.getElementById('tariff').value;
      const players = document.getElementById('players').value;
      const duration = document.getElementById('duration').value;
  
      let errors = [];
  
      if (!name) {
        errors.push('Пожалуйста, введите имя.');
      }
  
      const phonePattern = /^\+7\s\(\d{3}\)-\d{3}-\d{2}-\d{2}$/;
      if (!phone || !phonePattern.test(phone)) {
        errors.push('Пожалуйста, введите корректный номер телефона.');
      }
  
      if (!date) {
        errors.push('Пожалуйста, выберите дату.');
      } else {
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (selectedDate < today) {
          errors.push('Дата не может быть в прошлом.');
        }
      }
  
      if (!time) {
        errors.push('Пожалуйста, выберите время.');
      }
  
      if (!tariff) {
        errors.push('Пожалуйста, выберите тариф.');
      }
  
      if (tariff === 'Открытая игра') {
        if (!players || parseInt(players) < 2 || parseInt(players) > 10) {
          errors.push('Пожалуйста, выберите количество игроков от 2 до 10.');
        }
  
        if (!duration) {
          errors.push('Пожалуйста, выберите продолжительность игры.');
        }
      }
  
      if (errors.length > 0) {
        alert(errors.join('\n'));
        event.preventDefault(); // Остановим отправку формы, если есть ошибки
      }
    });
  });
  