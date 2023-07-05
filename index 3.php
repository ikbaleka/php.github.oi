<?php

// Koneksi ke database
$host = 'sql304.infinityfree.com';
$dbname = 'if0_34499323_bukutamu';
$username = 'if0_34499323';
$password = 'UMcD0iBJPzcr3';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Koneksi database gagal: " . $e->getMessage();
    exit;
}

// Mendapatkan semua data bukutamu dari database
function getAllBukuTamu($conn) {
    $stmt = $conn->prepare("SELECT * FROM tbl_bukutamu");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// Mendapatkan semua data bukutamu dari database
$bukutamu = getAllBukuTamu($conn);

// Fungsi untuk menghapus data bukutamu berdasarkan ID
function deleteBukuTamu($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM tbl_bukutamu WHERE no = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

// Proses delete row data bukutamu
if (isset($_POST['delete_submit'])) {
    $id = $_POST['delete_id'];
    deleteBukuTamu($conn, $id);
    header("Location: index.php");
    exit;
}

// Proses insert data bukutamu
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $kota = $_POST['kota'];

    $stmt = $conn->prepare("INSERT INTO tbl_bukutamu (nama, email, kota) VALUES (:nama, :email, :kota)");
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':kota', $kota);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Proses edit data bukutamu
if (isset($_POST['edit_submit'])) {
    $id = $_POST['edit_id'];
    $nama = $_POST['edit_nama'];
    $email = $_POST['edit_email'];
    $kota = $_POST['edit_kota'];

    $stmt = $conn->prepare("UPDATE tbl_bukutamu SET nama = :nama, email = :email, kota = :kota WHERE no = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':kota', $kota);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD dengan Login</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Daftar Buku Tamu</h2>
    <p> Tambah Data Buku Tamu </p>
    <button onclick="openInsertModal()">Tambah</button>
    <a href="http://arfandwimadya.lovestoblog.com/">
  <button>Home</button>
</a>
    <br><br>
    <table id="bukutamuTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Kota</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bukutamu as $row) : ?>
                <tr>
                    <td><?php echo $row['no']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['kota']; ?></td>
                    <td>
                        <a href="#" onclick="openEditModal(<?php echo $row['no']; ?>, '<?php echo $row['nama']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['kota']; ?>')">Edit</a>
                        <a href="#" onclick="openDeleteModal(<?php echo $row['no']; ?>)">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="insertModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeInsertModal()">&times;</span>
            <h3>Form Tambah Buku Tamu</h3>
            <form method="POST" action="">
                <label>Nama:</label>
                <input type="text" name="nama" required><br><br>
                <label>Email:</label>
                <input type="email" name="email" required><br><br>
                <label>Kota:</label>
                <input type="text" name="kota" required><br><br>
                <input type="submit" name="submit" value="Simpan">
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>Form Edit Buku Tamu</h3>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="edit_id" id="edit_id">
                <label>Nama:</label>
                <input type="text" name="edit_nama" id="edit_nama" required><br><br>
                <label>Email:</label>
                <input type="email" name="edit_email" id="edit_email" required><br><br>
                <label>Kota:</label>
                <input type="text" name="edit_kota" id="edit_kota" required><br><br>
                <input type="submit" name="edit_submit" value="Simpan">
            </form>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <form id="deleteForm" method="POST" action="">
                <input type="hidden" name="delete_id" id="delete_id">
                <input type="submit" name="delete_submit" value="Hapus">
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#bukutamuTable').DataTable();
        });

        function openInsertModal() {
            var modal = document.getElementById('insertModal');
            modal.style.display = "block";
        }

        function closeInsertModal() {
            var modal = document.getElementById('insertModal');
            modal.style.display = "none";
        }

        function openEditModal(id, nama, email, kota) {
            var modal = document.getElementById('editModal');
            modal.style.display = "block";

            // Set value untuk form edit
            var edit_id = document.getElementById('edit_id');
            edit_id.value = id;

            var edit_nama = document.getElementById('edit_nama');
            var edit_email = document.getElementById('edit_email');
            var edit_kota = document.getElementById('edit_kota');

            edit_nama.value = nama;
            edit_email.value = email;
            edit_kota.value = kota;
        }

        function closeEditModal() {
            var modal = document.getElementById('editModal');
            modal.style.display = "none";
        }

        function openDeleteModal(id) {
            var modal = document.getElementById('deleteModal');
            modal.style.display = "block";

            // Set value untuk form delete
            var delete_id = document.getElementById('delete_id');
            delete_id.value = id;
        }

        function closeDeleteModal() {
            var modal = document.getElementById('deleteModal');
            modal.style.display = "none";
        }
    </script>
</body>
</html>
