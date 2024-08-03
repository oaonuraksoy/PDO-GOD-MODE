<?php
//Kullanım : 
$db = new Database();

// INSERT işlemi
$data = array(
    'name' => 'John Doe',
    'email' => 'johndoe@example.com',
    'phone' => '555-1234'
);
$db->insert('users', $data);

// UPDATE işlemi
$data = array(
    'name' => 'Jane Doe',
    'email' => 'janedoe@example.com',
    'phone' => '555-5678'
);
$id = 1;
$db->update('users', $data, $id);

// DELETE işlemi
$id = 1;
$db->delete('users', $id);

//GetData işlemi

// Tüm sütunları seç
$data = $db->getData("mytable");

// Belirli sütunları seç
$data = $db->getData("mytable", "id, name");

// Belirli sütunları seç ve WHERE koşulu belirle
$data = $db->getData("mytable", "*", "id=:id", array(":id" => 1));

// Formdan gelen dosyayı yükle
if(isset($_FILES['image'])) {
    $db = new Database();
    $result = $db->uploadImage($_FILES['image']);
    if($result) {
        // Yükleme işlemi başarılı olduğunda yapılacaklar
        echo "Dosya başarıyla yüklendi. Yol: " . $result['path'] . ", Dosya adı: " . $result['filename'];
    } else {
        // Yükleme işlemi başarısız olduğunda yapılacaklar
        echo "Dosya yüklenemedi.";
    }
}

?>
