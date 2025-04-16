<p>Kepada Admin Pemancingan Kameumeut, </p>
<p>Terdapat pemesanan lapak di pemancingan dengan detail sebagai berikut:</p>
<p>Booking ID : {{ $transaction->booking->id }}</p>
<p>Nama Pemesan : {{ $transaction->booking->customer_name }}</p>
<p>Metode Pembayaran : {{ $transaction->paymentMethodDP->name }}</p>
<a href="{{ route('admin.bookingIndex') }}">Klik di sini untuk melihat detail</a>
