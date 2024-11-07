<?php 
// Incluir el archivo de conexión
include 'conectar_db.php';

// Determinar el número total de registros
$sql_count = "SELECT COUNT(*) as total FROM empresas";
$result_count = mysqli_query($conn, $sql_count);
$total_row = mysqli_fetch_assoc($result_count);
$total_records = $total_row['total'];
$limit = 10; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Consulta para obtener las empresas con paginación
$sql = "SELECT * FROM empresas LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
$total_pages = ceil($total_records / $limit);

// Función para eliminar empresas
function deleteEmpresas($ids) {
    global $conn;
    $ids_string = implode(',', $ids);
    $sql_delete = "DELETE FROM empresas WHERE id IN ($ids_string)";
    mysqli_query($conn, $sql_delete);
    header("Location: consultar_empresas.php");
    exit();
}

// Manejo de la eliminación masiva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    deleteEmpresas(explode(',', $_POST['ids']));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Empresas</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 50px;
            height: auto;
        }
        .button-container {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }
        .pagination {
            margin-top: 15px;
            text-align: center;
        }
        #searchInput {
            width: 98%; 
            padding: 10px;
            margin-top: 10px; 
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'header_nav.php'; ?>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const matched = Array.from(cells).some(cell => {
                    return cell.textContent.toLowerCase().includes(filter);
                });
                row.style.display = matched ? '' : 'none';
            });
        }

        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
            updateDeleteList();
        }

        function confirmDelete() {
            return confirm('¿Está seguro que desea eliminar los registros seleccionados?');
        }

        function updateDeleteList() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            const ids = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    ids.push(checkbox.value);
                }
            });
            document.getElementById('ids').value = ids.join(',');
        }
    </script>

    <div class="container">
        <h2>Listado de Empresas</h2>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="alert" style="color: green; margin-bottom: 10px;">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <div class="button-container">
    <button onclick="window.location.href='index.php'" class="btn">Volver al inicio</button>
    <div class="right-buttons">
        <button onclick="window.location.href='registrar_empresa.php'" class="btn">Crear Empresa</button>
        <form method="POST" onsubmit="return confirmDelete();" style="display: inline;">
            <button type="submit" name="delete_selected" class="btn">Eliminar Seleccionados</button>
            <input type="hidden" name="ids" id="ids" value="">
        </form>
    </div>
</div>



        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Buscar...">
        
        <table>
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Razón Social</th>
                    <th>NIT</th>
                    <th>Enlaces</th>
                    <th>Acciones</th>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <?php if (!empty($row['logo_path'])): ?>
                            <img src="uploads/logos/<?php echo htmlspecialchars($row['logo_path']); ?>" alt="Logo">
                        <?php else: ?>
                            <p>Logo no disponible</p>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['razon_social']); ?></td>
                    <td><?php echo htmlspecialchars($row['nit']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($row['pagina_web']); ?>" target="_blank">Página Web</a> | 
                        <a href="<?php echo htmlspecialchars($row['intranet']); ?>" target="_blank">Intranet</a> | 
                        <a href="<?php echo htmlspecialchars($row['carpeta_sig']); ?>" target="_blank">Carpeta SIG</a>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <button onclick="window.location.href='ver_empresa.php?id=<?php echo $row['id']; ?>'">Consultar</button>
                            <button onclick="if(confirm('¿Está seguro que desea eliminar esta empresa?')) window.location.href='eliminar_empresa.php?id=<?php echo $row['id']; ?>'">Eliminar</button>
                        </div>
                    </td>
                    <td><input type="checkbox" value="<?php echo $row['id']; ?>" onclick="updateDeleteList()"></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
