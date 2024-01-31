#include <WiFi.h>
#include <HTTPClient.h>

#define TRIGGER_PIN  13
#define ECHO_PIN     12

const int MAX_HEIGHT = 20;
const int MIN_HEIGHT = 0;
String sensorID = "WL001";  // Ganti dengan ID sensor Anda

const char* ssid = "TSI_IoT";
const char* password = "tsispot123";
const char* serverURL = "http://203.190.53.159/ma/tes2/api.php"; // Ganti dengan alamat server dan endpoint yang sesuai

void setup() {
  Serial.begin(9600);
  pinMode(TRIGGER_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);

  // Koneksi WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
}

void loop() {
  long duration, distance;

  // Kirim sinyal trigger
  digitalWrite(TRIGGER_PIN, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIGGER_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIGGER_PIN, LOW);

  // Baca waktu pulsa dari sinyal echo
  duration = pulseIn(ECHO_PIN, HIGH);

  // Hitung jarak berdasarkan waktu pulsa
  distance = (duration * 0.0343) / 2;

  // Batasi nilai tinggi air antara MIN_HEIGHT dan MAX_HEIGHT
  distance = constrain(distance, MIN_HEIGHT, MAX_HEIGHT);

  // Hitung persentase tinggi air
  int waterLevelPercentage = map(distance, MIN_HEIGHT, MAX_HEIGHT, 100, 0);

  // Tampilkan hasil di Serial Monitor
  Serial.print("Tinggi Air: ");
  Serial.print(MAX_HEIGHT - distance);
  Serial.print(" cm | Persentase: ");
  Serial.print(waterLevelPercentage);
  Serial.println("%");

  // Kirim data ke server jika terhubung ke WiFi
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Format data untuk dikirim
    String postData = "idhardware=" + sensorID + "&tinggi_air=" + String(MAX_HEIGHT - distance) + "&persentase=" + String(waterLevelPercentage);

    // Kirim HTTP POST request ke server
    http.begin(serverURL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpResponseCode = http.POST(postData);

    // Tampilkan hasil dari server
    Serial.println("HTTP Response Code: " + String(httpResponseCode));
    Serial.println("Server Response: " + http.getString());

    // Selesai dengan request
    http.end();
  } else {
    Serial.println("WiFi Disconnected");
  }

  // Tunggu 1 detik sebelum membaca ulang
  delay(1000);
}
