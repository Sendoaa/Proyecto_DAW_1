class Calendar {

    addEventListenerToTimeInput() {
        const timeInput = document.getElementById('timeInput');
        const hiddenHoraSalida = document.getElementById('hidden_horaSalida');

        if (timeInput) {
            timeInput.addEventListener('input', e => {
                const timeValue = e.target.value;
                hiddenHoraSalida.value = timeValue; // Actualiza el campo oculto con la hora

                if (this.selectedDate) {
                    const fechaSinHora = this.selectedDate.format('DD [de] MMMM [de] YYYY');
                    const fechaYHora = `${fechaSinHora} ${timeValue}`;
                    console.log(fechaYHora);
                } else {
                    console.log('No se ha seleccionado ninguna fecha.');
                }
            });
        }
    }

    getElement() {
        return this.elCalendar;
    }
}
