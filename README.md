### Pannellum TYPO3 Extension (diw/pannellum)

Diese Extension integriert Pannellum (JS 360°/VR Panorama Viewer) in TYPO3 v12/v13. 
Die Inhalte werden über ein Plugin (Inhaltselement), eigenständige „Scene“-Datensätze und deren Hotspots konfiguriert. 
Mehrere Panoramen pro Seite werden unterstützt.

---

### Installation
1) Über Composer installieren (falls nicht bereits vorhanden):
```
composer require diw/pannellum
```
2) Extension im TYPO3-Backend aktivieren (Extension Manager).
3) Datenbank-Upgrade ausführen (Install Tool → Datenbank vergleichen)
4) Caches leeren.

Hinweise
- Pannellum-JS/CSS werden automatisch per CDN über Fluid `f:asset` eingebunden und dedupliziert.
- Falls Sie eine feste Pannellum-Version oder SRI-Hashes wünschen, kann dies in der Template-Integration angepasst werden.

---

### Handbuch für Redakteur:innen

Die Konfiguration gliedert sich in drei Bausteine, die zusammenspielen:

1) Scene-Datensätze anlegen (Inhalte vorbereiten)
- Legen Sie einen SysOrdner für „Scenes“ an.
- Erstellen Sie dort beliebig viele Datensätze vom Typ „Scene“.
- Pro Scene wählen Sie das Panorama-Bild (FAL-Datei), vergeben einen eindeutigen `identifier` und einen `title`.
- Im Reiter „Hotspots“ fügen Sie nach Bedarf Hotspots hinzu (z. B. Szenenwechsel oder Info-Links).

2) Plugin platzieren und Szenen auswählen
- Fügen Sie das Inhaltselement „Plugins → 360Grad Panorama“ (list_type: `pannellum_panorama`) ein.
- Reiter „Szenen“: Wählen Sie die gewünschten Scene-Datensätze in der gewünschten Reihenfolge. Die erste ausgewählte Scene wird automatisch als `firstScene` verwendet. Wenn in einer Scene Hotspot-Links zu einer anderen Scene enthalten sind die hier nicht gewählt wurde dann wird der betreffende Hotsot nicht angezeigt.
- Reiter „Defaults“ und „Options“: Pflegen Sie globale Anzeige- und Steuerungsoptionen (siehe unten).
- Am Inhaltselement steht zusätzlich ein FAL-Feld „Vorschaubild“ zur Verfügung 

3) Frontend-Ausgabe
- Das Element rendert eine eindeutige Container-ID pro Inhaltselement (mehrere Panoramen pro Seite sind möglich).
- Die Initialisierung erfolgt automatisch, sobald die Pannellum-Bibliothek geladen ist.

---

### Optionen im Detail

#### Plugin – Reiter „Defaults“
- sceneFadeDuration (number)
  - Überblenddauer zwischen Szenen in Millisekunden.
- autoLoad (boolean)
  - Lädt das Panorama automatisch beim Laden der Seite.
- autoRotate (number)
  - Startet automatische Rotation mit angegebener Geschwindigkeit (negativ = gegen Uhrzeigersinn).
- author (string)
  - Autor:in, die in der Oberfläche angezeigt wird.
- preview (FAL am Inhaltselement)
  - Vorschau-/Ladebild. Wird nur gesetzt, wenn eine Datei referenziert ist.

Hinweis: `firstScene` ist kein Eingabefeld – es wird automatisch auf die zuerst im Plugin ausgewählte Scene gesetzt.

#### Plugin – Reiter „Options“
- authorURL (string)
  - Wenn gesetzt, wird der `author`-Text auf diese URL verlinkt. Wirkt nur, wenn `author` gesetzt ist.
- autoRotateInactivityDelay (number)
  - Verzögerung (ms), nach der nach Nutzeraktivität die Auto-Rotation startet; nur wirksam mit `autoRotate`.
- autoRotateStopDelay (number)
  - Verzögerung (ms), nach der die Auto-Rotation nach dem Laden stoppt; nur wirksam mit `autoRotate`.
- orientationOnByDefault (boolean)
  - Aktiviert die Geräteorientierung beim Laden (falls unterstützt). Standard: false.
- showZoomCtrl (boolean)
  - Blendet Zoom-Steuerung ein/aus. Standard: true.
- keyboardZoom (boolean)
  - Aktiviert/Deaktiviert Zoomen per Tastatur. Standard: true.
- mouseZoom (boolean|string)
  - `true`/`false` oder `fullscreenonly` (nur im Vollbild zoomen). Standard: true.
- draggable (boolean)
  - Aktiviert/Deaktiviert Ziehen per Maus/Touch. Standard: true.
- disableKeyboardCtrl (boolean)
  - Deaktiviert alle Tastatursteuerungen. Standard: false.
- showFullscreenCtrl (boolean)
  - Steuert Anzeige des Vollbild-Buttons (nur bei Browser-Support). Standard: true.
- showControls (boolean)
  - Blendet alle Controls ein/aus. Standard: true.

#### Scene-Datensatz – Allgemeines
- identifier (string)
  - Eindeutiger Schlüssel der Scene; wird in Hotspots referenziert und im JSON als Szenen-ID genutzt.
- title (string)
  - Titel der Scene (für Redaktionszwecke und optional UI).
- type (string)
  - Pannellum-Szenentyp; üblich ist `equirectangular`.
- panorama (FAL-Datei)
  - Das 360°-Panorama-Bild (Dateiauswahl über TYPO3 FAL).
- hotspot_debug (boolean)
  - Aktiviert die Hotspot-Debug-Anzeige (nur für Entwicklung/Debugging sinnvoll).

#### Scene-Datensatz – Start-/Grenzwerte der Ansicht
- yaw (number)
  - Start-Yaw (in Grad). Standard: 0.
- pitch (number)
  - Start-Pitch (in Grad). Standard: 0.
- hfov (number)
  - Start-HFOV (Sichtfeld horizontal, in Grad). Standard: 100.
- minYaw / maxYaw (number)
  - Minimaler/Maximaler Yaw der Viewer-Kante (in Grad). Standard: -180 / 180 (kein Limit).
- minPitch / maxPitch (number)
  - Minimaler/Maximaler Pitch der Viewer-Kante (in Grad). Standard: unbegrenzt (Center kann -90/90 erreichen).
- minHfov / maxHfov (number)
  - Minimaler/Maximaler HFOV (in Grad). Standard: 50 / 120.

Leere Felder werden nicht ins JSON geschrieben; Pannellum verwendet dann seine Standardwerte.

#### Hotspots (in der Scene-FlexForm)
- pitch (number)
  - Position des Hotspots (Pitch) in Grad.
- yaw (number)
  - Position des Hotspots (Yaw) in Grad.
- type (select: scene | info)
  - „scene“ wechselt zu einer Szene; „info“ verlinkt zu einer URL.
- text (string)
  - Bezeichner/Tooltip des Hotspots.
- sceneId (select)
  - Ziel-Szene (Identifier) für Scene-Hotspots. Auswahl aus vorhandenen Scenes (alphabetisch sortiert).
- url (string)
  - Ziel-URL für Info-Hotspots. Für Scene-Hotspots nicht anwendbar. Ausgabe im JSON-Schlüssel `URL` (Großschreibung gemäß Pannellum).
- targetPitch (number | "same")
  - Ziel-Pitch der Scene in Grad. "same" übernimmt aktuellen Pitch der Ausgangsszene.
- targetYaw (number | "same" | "sameAzimuth")
  - Ziel-Yaw der Scene in Grad. "same" übernimmt aktuellen Yaw; "sameAzimuth" berücksichtigt `northOffset` zur Beibehaltung der Nordausrichtung.
- targetHfov (number | "same")
  - Ziel-HFOV in Grad. "same" übernimmt aktuellen HFOV der Ausgangsszene.

---

### Support / Weiterentwicklung
Fehler gefunden oder Wünsche? Bitte ein Issue im Repository anlegen oder direkt Kontakt aufnehmen. 
