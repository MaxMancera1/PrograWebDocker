<?php
// Modelo
require_once __DIR__ . '/../sistema.class.php';

class Empleado extends Sistema {   
    function updateDatos($id){
    $datos = [];
    $datos['primer_apellido'] = $this->cargarArchivo('primer_apellido', '../../img/uploads/');
    if (is_null($datos['primer_apellido'])) {
        return ['error' => 'Primer Apellido no encontrado'];
    }
    $datos['segundo_apellido'] = $this->cargarArchivo('segundo_apellido', '../../img/uploads/');
    if (is_null($datos['segundo_apellido'])) {
        return ['error' => 'Segundo Apellido no encontrado'];
    }   
    $datos['rfc'] = $this->cargarArchivo('rfc', '../../img/uploads/');
    if (is_null($datos['rfc'])) {
        return ['error' => 'RFC no encontrado'];
    }
    $datos['correo'] = $this->cargarArchivo('correo', '../../img/uploads/');
    if (is_null($datos['correo'])) {
        return ['error' => 'Correo no encontrado'];
    }
    $datos['contraseña'] = $this->cargarArchivo('contraseña', '../../img/uploads/');
    if (is_null($datos['contraseña'])) {
        return ['error' => 'Contraseña no encontrado'];
    }
    $datos = [];
        $sql = "UPDATE empleados SET primer_apellido = :primer_apellido, segundo_apellido = :segundo_apellido WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam("nombre", $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":primer_apellido", $datos['primer_apellido'], PDO::PARAM_STR);
        $stmt->bindParam(":segundo_apellido", $datos['segundo_apellido'], PDO::PARAM_STR);
        $stmt->bindParam(":rfc", $datos['rfc'], PDO::PARAM_STR);
        $stmt->bindParam(":correo", $datos['correo'], PDO::PARAM_STR);
        $stmt->bindParam(":contraseña", $datos['contraseña'], PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $id;
        return $datos;

    }
        
    function create($data){
        $datos = [];
        $archivo = $_FILES['foto'] ?? null;
        $ext = strtolower(pathinfo($archivo['name'] ?? '', PATHINFO_EXTENSION));
        if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK || $archivo['type'] !== 'image/jpeg' || !in_array($ext, ['jpg', 'jpeg'], true)) {
            return ['error' => 'Foto no encontrada'];
        }
        $foto = file_get_contents($archivo['tmp_name']);
        if ($foto === false || $foto === '') {
            return ['error' => 'Foto no encontrada'];
        }

        $stmt = $this->db->prepare("SELECT id_rol FROM rol WHERE rol = :rol");
        $nombreRol = 'Empleado';
        $stmt->bindParam(':rol', $nombreRol, PDO::PARAM_STR);
        $stmt->execute();
        $idRol = $stmt->fetchColumn();
        if (!$idRol) {
            return ['error' => 'Error al crear el empleado'];
        }

        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->beginTransaction();
        try  {
            $sql = "INSERT INTO usuario (correo, contrasena) VALUES(:correo, :contrasena) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":correo", $data['correo'], PDO::PARAM_STR);
            $contrasena = md5($data['contrasena']);
            $stmt->bindParam(":contrasena", $contrasena, PDO::PARAM_STR);
            $stmt->execute();
            $idUsuario = (int) $this->db->lastInsertId();

            $sql = "INSERT INTO usuario_rol (id_usuario, id_rol) VALUES(:id_usuario, :id_rol) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(":id_rol", $idRol, PDO::PARAM_INT);
            $stmt->execute();
         

            $sql = "INSERT INTO empleado (id_usuario, nombre, primer_apellido, segundo_apellido, rfc, foto) VALUES(:id_usuario, :nombre, :primer_apellido, :segundo_apellido, :rfc, :foto) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(":primer_apellido", $data['primer_apellido'], PDO::PARAM_STR);
            $stmt->bindParam(":segundo_apellido", $data['segundo_apellido'], PDO::PARAM_STR);
            $stmt->bindParam(":rfc", $data['rfc'], PDO::PARAM_STR);
            $stmt->bindValue(":foto", $foto, PDO::PARAM_LOB);
            $stmt->execute();
          

            $this->db->commit();
            return ['success' => 'Empleado creado correctamente'];
        }
        catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['error' => 'Error al crear el empleado'];
        }
    }
    
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT * FROM marcas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }
    function getFoto($id){
        if (!is_numeric($id)) {
            return null;
        }
        $sql = "SELECT foto FROM empleado WHERE id_empleado = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila['foto'] ?? null;
    }
    function getAll(){
        $datos = [];
        $sql = "SELECT empleado.id_empleado, empleado.nombre, empleado.primer_apellido, empleado.segundo_apellido, empleado.rfc, empleado.id_usuario, (empleado.foto IS NOT NULL) AS tiene_foto, usuario.correo AS correo
                FROM empleado
                LEFT JOIN usuario ON empleado.id_usuario = usuario.id_usuario";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $logo = $this->cargarArchivo('logo', '../../img/uploads/');
            $intentoLogo = isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE;
            if ($intentoLogo && is_null($logo)) {
                $actual = $this->getOne($id);
                $actual['error'] = $_FILES['logo']['error'] === UPLOAD_ERR_INI_SIZE
                    ? 'La imagen es demasiado grande'
                    : 'No se pudo guardar la imagen';
                return $actual;
            }
            if (is_null($logo)) {
                $actual = $this->getOne($id);
                $logo = $actual['logo'] ?? '';
            }
            $sql = "UPDATE marcas SET marca = :marca, logo = :logo, id_proveedor = :id_proveedor WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":marca", $data['marca'], PDO::PARAM_STR);
            $stmt->bindParam(":logo", $logo, PDO::PARAM_STR);
            $idProveedor = ($data['id_proveedor'] ?? '') === '' ? null : $data['id_proveedor'];
            $stmt->bindValue(":id_proveedor", $idProveedor, $idProveedor === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;

    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "DELETE FROM marcas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }

    function credencialPdf($id) {
        if (!is_numeric($id)) {
            return null;
        }
        $sql = "SELECT empleado.id_empleado, empleado.nombre, empleado.primer_apellido, empleado.segundo_apellido, empleado.rfc, empleado.foto, usuario.correo AS correo
                FROM empleado
                LEFT JOIN usuario ON empleado.id_usuario = usuario.id_usuario
                WHERE empleado.id_empleado = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$empleado) {
            return null;
        }
        $font = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
        $fontBold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
        $fontSerif = '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf';
        if (!is_file($font) || !is_file($fontBold) || !is_file($fontSerif)) {
            return null;
        }
        $jpeg = $this->dibujarCredencial($empleado, $font, $fontBold, $fontSerif);
        $img = imagecreatefromstring($jpeg);
        $pdf = $this->jpegAPdf($jpeg, imagesx($img), imagesy($img));
        imagedestroy($img);
        return $pdf;
    }

    function dibujarCredencial($empleado, $font, $fontBold, $fontSerif) {
        $w = 680;
        $h = 1060;
        $im = imagecreatetruecolor($w, $h);
        imagealphablending($im, true);
        $cream = imagecolorallocate($im, 246, 241, 232);
        $green = imagecolorallocate($im, 31, 77, 58);
        $greenDark = imagecolorallocate($im, 20, 52, 43);
        $copper = imagecolorallocate($im, 196, 107, 58);
        $ink = imagecolorallocate($im, 28, 25, 21);
        $muted = imagecolorallocate($im, 107, 98, 88);
        $white = imagecolorallocate($im, 246, 241, 232);
        imagefilledrectangle($im, 0, 0, $w, $h, $cream);
        imagefilledrectangle($im, 0, 0, $w, 470, $green);
        imagefilledrectangle($im, 0, 0, $w, 10, $copper);
        imagefilledellipse($im, -40, -30, 280, 280, $greenDark);
        imagefilledellipse($im, 700, 80, 220, 220, $greenDark);

        $this->textoCentro($im, 15, 78, $font, $white, 'CLUSTER', 8);
        $this->textoCentro($im, 40, 150, $fontSerif, $white, 'Credencial');
        $this->textoCentro($im, 16, 196, $font, $white, 'Identificación de personal');

        $cx = (int) ($w / 2);
        $cy = 360;
        $diam = 228;
        imagefilledellipse($im, $cx, $cy, $diam + 22, $diam + 22, $copper);
        imagefilledellipse($im, $cx, $cy, $diam + 12, $diam + 12, $cream);
        $foto = $empleado['foto'] ?? '';
        if ($foto === '' || !$this->pegarFotoCircular($im, $foto, $cx, $cy, $diam)) {
            imagefilledellipse($im, $cx, $cy, $diam, $diam, $greenDark);
            $iniciales = mb_strtoupper(mb_substr($empleado['nombre'], 0, 1) . mb_substr($empleado['primer_apellido'], 0, 1));
            $this->textoCentro($im, 54, $cy + 20, $fontSerif, $white, $iniciales);
        }

        $nombre = $empleado['nombre'];
        $apellidos = trim($empleado['primer_apellido'] . ' ' . $empleado['segundo_apellido']);
        $this->textoCentro($im, $this->tamanoQueCabe($nombre, $fontSerif, 36, 600), 560, $fontSerif, $ink, $nombre);
        $this->textoCentro($im, $this->tamanoQueCabe($apellidos, $font, 20, 600), 604, $font, $muted, $apellidos);
        imagefilledrectangle($im, $cx - 36, 636, $cx + 36, 640, $copper);

        $filas = [
            ['RFC', $empleado['rfc']],
            ['CORREO', $empleado['correo'] ?: '—'],
            ['NO. EMPLEADO', str_pad((string) $empleado['id_empleado'], 4, '0', STR_PAD_LEFT)],
        ];
        $y = 700;
        foreach ($filas as $fila) {
            $this->textoCentro($im, 12, $y, $font, $muted, $fila[0], 3);
            $this->textoCentro($im, $this->tamanoQueCabe($fila[1], $fontBold, 22, 600), $y + 36, $fontBold, $ink, $fila[1]);
            $y += 88;
        }

        imagefilledrectangle($im, 0, 980, $w, $h, $green);
        imagefilledrectangle($im, 0, 980, $w, 986, $copper);
        $this->textoCentro($im, 13, 1030, $font, $white, 'PERSONAL AUTORIZADO', 2);

        ob_start();
        imagejpeg($im, null, 90);
        $jpeg = ob_get_clean();
        imagedestroy($im);
        return $jpeg;
    }

    function pegarFotoCircular($im, $jpeg, $cx, $cy, $diam) {
        $src = @imagecreatefromstring($jpeg);
        if (!$src) {
            return false;
        }
        $side = min(imagesx($src), imagesy($src));
        $sq = imagecreatetruecolor($diam, $diam);
        imagecopyresampled($sq, $src, 0, 0, (int) ((imagesx($src) - $side) / 2), (int) ((imagesy($src) - $side) / 2), $diam, $diam, $side, $side);
        imagedestroy($src);
        $r = $diam / 2;
        $ox = (int) ($cx - $r);
        $oy = (int) ($cy - $r);
        for ($y = 0; $y < $diam; $y++) {
            $dy = $y - $r + 0.5;
            $reach = $r * $r - $dy * $dy;
            if ($reach < 0) {
                continue;
            }
            $dx = (int) floor(sqrt($reach));
            $x1 = (int) ($r - $dx);
            imagecopy($im, $sq, $ox + $x1, $oy + $y, $x1, $y, $dx * 2, 1);
        }
        imagedestroy($sq);
        return true;
    }

    function textoCentro($im, $size, $y, $font, $color, $text, $tracking = 0) {
        $chars = preg_split('//u', (string) $text, -1, PREG_SPLIT_NO_EMPTY);
        $widths = [];
        $total = 0;
        foreach ($chars as $char) {
            $box = imagettfbbox($size, 0, $font, $char);
            $cw = $box[2] - $box[0];
            $widths[] = $cw;
            $total += $cw + $tracking;
        }
        if ($chars) {
            $total -= $tracking;
        }
        $x = (imagesx($im) - $total) / 2;
        foreach ($chars as $i => $char) {
            imagettftext($im, $size, 0, (int) $x, (int) $y, $color, $font, $char);
            $x += $widths[$i] + $tracking;
        }
    }

    function tamanoQueCabe($text, $font, $size, $maxW) {
        while ($size > 12) {
            $box = imagettfbbox($size, 0, $font, (string) $text);
            if (($box[2] - $box[0]) <= $maxW) {
                break;
            }
            $size--;
        }
        return $size;
    }

    function jpegAPdf($jpeg, $pxW, $pxH) {
        $pageW = 288;
        $pageH = round($pageW * $pxH / $pxW, 2);
        $content = "q $pageW 0 0 $pageH 0 0 cm /Im0 Do Q\n";
        $objs = [
            1 => "<< /Type /Catalog /Pages 2 0 R >>",
            2 => "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            3 => "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 $pageW $pageH] /Contents 4 0 R /Resources << /XObject << /Im0 5 0 R >> >> >>",
            4 => "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream",
            5 => "<< /Type /XObject /Subtype /Image /Width $pxW /Height $pxH /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($jpeg) . " >>\nstream\n" . $jpeg . "\nendstream",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $i => $body) {
            $offsets[$i] = strlen($pdf);
            $pdf .= "$i 0 obj\n$body\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        for ($i = 1; $i <= 5; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
        return $pdf;
    }

    function reportePdf() {
        $sql = "SELECT empleado.id_empleado, empleado.nombre, empleado.primer_apellido, empleado.segundo_apellido, empleado.rfc, empleado.foto, usuario.correo AS correo
                FROM empleado
                LEFT JOIN usuario ON empleado.id_usuario = usuario.id_usuario
                ORDER BY empleado.id_empleado";
        $empleados = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $pdf = new ReporteEmpleadosPdf();
        $pdf->total = count($empleados);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(12, 28, 12);
        $pdf->AddPage();
        if (!$empleados) {
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->Cell(0, 10, $pdf->txt('No hay empleados registrados.'), 0, 1);
            return $pdf->Output('S');
        }
        $fill = false;
        foreach ($empleados as $empleado) {
            $alto = 14;
            if ($pdf->GetY() + $alto > $pdf->GetPageHeight() - 16) {
                $pdf->AddPage();
            }
            $y = $pdf->GetY();
            $x = 12;
            $pdf->SetFillColor($fill ? 246 : 255, $fill ? 241 : 255, $fill ? 232 : 255);
            $pdf->Rect($x, $y, array_sum($pdf->anchos), $alto, 'F');
            $pdf->SetDrawColor(215, 207, 195);
            $borde = $x;
            foreach ($pdf->anchos as $ancho) {
                $pdf->Line($borde, $y, $borde, $y + $alto);
                $borde += $ancho;
            }
            $pdf->Line($borde, $y, $borde, $y + $alto);
            $pdf->Line($x, $y + $alto, $borde, $y + $alto);
            $pdf->SetFont('Helvetica', '', 10);
            $pdf->SetTextColor(28, 25, 21);
            $valores = [
                $empleado['id_empleado'],
                $empleado['nombre'],
                $empleado['primer_apellido'],
                $empleado['segundo_apellido'],
                $empleado['rfc'],
                $empleado['correo'] ?: '',
            ];
            foreach ($valores as $i => $valor) {
                $pdf->SetXY($x, $y + 3);
                $pdf->Cell($pdf->anchos[$i], 8, $pdf->txt($valor), 0, 0, $i === 0 ? 'C' : 'L');
                $x += $pdf->anchos[$i];
            }
            $foto = $empleado['foto'] ?? '';
            if (is_string($foto) && strncmp($foto, "\xFF\xD8", 2) === 0) {
                $tmp = sys_get_temp_dir() . '/emp-' . $empleado['id_empleado'] . '-' . uniqid() . '.jpg';
                file_put_contents($tmp, $foto);
                $pdf->Image($tmp, $x + 5, $y + 1, 12, 12, 'JPG');
                unlink($tmp);
            }
            $pdf->SetY($y + $alto);
            $fill = !$fill;
        }
        return $pdf->Output('S');
    }
}

class ReporteEmpleadosPdf extends FPDF {
    public $anchos = [16, 42, 42, 42, 36, 73, 22];
    public $total = 0;

    function __construct() {
        parent::__construct('L', 'mm', 'A4');
    }

    function Header() {
        $this->SetFillColor(31, 77, 58);
        $this->Rect(0, 0, $this->GetPageWidth(), 16, 'F');
        $this->SetFillColor(196, 107, 58);
        $this->Rect(0, 16, $this->GetPageWidth(), 1.2, 'F');
        $this->SetTextColor(246, 241, 232);
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetXY(12, 4);
        $this->Cell(180, 8, $this->txt('Reporte de empleados'), 0, 0);
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 8, $this->txt(date('d/m/Y')), 0, 0, 'R');
        $this->SetFillColor(20, 52, 43);
        $this->SetFont('Helvetica', 'B', 9);
        $this->SetXY(12, 20);
        $titulos = ['#', 'Nombre', 'Primer apellido', 'Segundo apellido', 'RFC', 'Correo', 'Foto'];
        foreach ($titulos as $i => $titulo) {
            $this->Cell($this->anchos[$i], 8, $this->txt($titulo), 0, 0, $i === 0 ? 'C' : 'L', true);
        }
        $this->SetY(28);
    }

    function Footer() {
        $this->SetY(-12);
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(107, 98, 88);
        $this->Cell(120, 8, $this->txt($this->total . ' empleados'), 0, 0);
        $this->Cell(0, 8, $this->txt('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function txt($s) {
        return mb_convert_encoding((string) $s, 'ISO-8859-1', 'UTF-8');
    }
}

?>
