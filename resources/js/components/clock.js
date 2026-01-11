export function initRealtimeClock() {
    const clockElement = document.getElementById('realtime-clock');

    // Keluar jika elemen jam tidak ditemukan di DOM
    if (!clockElement) {
        return;
    }

    // Fungsi untuk memperbarui jam dan menampilkannya di DOM
    const updateClock = () => {
        // Mendapatkan waktu saat ini
        const now = new Date();

        // Opsi format untuk tanggal dan waktu (menggunakan lokal 'id-ID')
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hourCycle: 'h23',
            timeZone: 'Asia/Jakarta' // Ganti jika zona waktu Anda berbeda (mis: Asia/Makassar atau Asia/Jayapura)
        };

        // Memformat waktu ke bahasa Indonesia
        let formattedTime = new Intl.DateTimeFormat('id-ID', options).format(now);

        // Ubah pemisah waktu 13.12.18 → 13:12:18
        formattedTime = formattedTime.replace(/(\d{2})\.(\d{2})\.(\d{2})/, '$1:$2:$3');

        // Memperbarui konten elemen
        clockElement.textContent = formattedTime;
    };

    // Panggil sekali untuk menampilkan waktu awal
    updateClock();

    // Atur interval untuk memperbarui jam setiap 1 detik
    setInterval(updateClock, 1000);
};