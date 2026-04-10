<?php

declare(strict_types=1);

namespace Diw\Pannellum\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use Doctrine\DBAL\Connection;
use TYPO3\CMS\Core\Resource\FileRepository;

class PannellumController extends ActionController
{
    public function showAction(): ResponseInterface
    {
        // Build Pannellum configuration from FlexForm settings
        $settings = $this->settings ?? [];

        // Determine current content element UID for unique DOM ids
        $contentElementId = 0;
        $contentObject = $this->configurationManager->getContentObject();
        if ($contentObject && isset($contentObject->data['uid'])) {
            $contentElementId = (int)$contentObject->data['uid'];
        }

        $defaults = [
            // 'firstScene' is determined automatically from the first selected scene below
            'sceneFadeDuration' => (int)($settings['default']['sceneFadeDuration'] ?? 3000),
            'autoLoad' => (bool)($settings['default']['autoLoad'] ?? true),
            'autoRotate' => (int)($settings['default']['autoRotate'] ?? -2),
            'author' => (string)($settings['default']['author'] ?? ''),
        ];

        // Additional options from FlexForm (Options sheet)
        // Only set if provided, so Pannellum defaults apply otherwise
        $authorURL = trim((string)($settings['default']['authorURL'] ?? ''));
        if ($authorURL !== '') {
            $defaults['authorURL'] = $authorURL;
        }

        $inactivityDelay = $settings['default']['autoRotateInactivityDelay'] ?? '';
        if ($inactivityDelay !== '' && $inactivityDelay !== null) {
            $defaults['autoRotateInactivityDelay'] = (int)$inactivityDelay;
        }
        $stopDelay = $settings['default']['autoRotateStopDelay'] ?? '';
        if ($stopDelay !== '' && $stopDelay !== null) {
            $defaults['autoRotateStopDelay'] = (int)$stopDelay;
        }

        foreach (['orientationOnByDefault','showZoomCtrl','keyboardZoom','draggable','disableKeyboardCtrl','showFullscreenCtrl','showControls'] as $boolKey) {
            if (array_key_exists($boolKey, $settings['default'] ?? [])) {
                $defaults[$boolKey] = (bool)$settings['default'][$boolKey];
            }
        }

        // mouseZoom can be boolean true/false or string "fullscreenonly"
        $mouseZoom = $settings['default']['mouseZoom'] ?? '';
        if ($mouseZoom !== '' && $mouseZoom !== null) {
            $mouseZoomStr = is_bool($mouseZoom) ? ($mouseZoom ? 'true' : 'false') : (string)$mouseZoom;
            switch (strtolower($mouseZoomStr)) {
                case 'true':
                    $defaults['mouseZoom'] = true;
                    break;
                case 'false':
                    $defaults['mouseZoom'] = false;
                    break;
                case 'fullscreenonly':
                    $defaults['mouseZoom'] = 'fullscreenonly';
                    break;
                default:
                    // ignore invalid value
                    break;
            }
        }

        // Resolve preview image URL via FAL on current tt_content (field tx_pannellum_preview)
        $previewUrl = '';
        if ($contentElementId > 0) {
            try {
                /** @var FileRepository $fileRepository */
                $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
                $fileRefs = $fileRepository->findByRelation('tt_content', 'tx_pannellum_preview', $contentElementId);
                if (!empty($fileRefs)) {
                    $firstRef = $fileRefs[0];
                    $fileObject = $firstRef->getOriginalFile();
                    $publicUrl = $fileObject->getPublicUrl();
                    if (is_string($publicUrl) && $publicUrl !== '') {
                        $previewUrl = $publicUrl;
                    }
                }
            } catch (\Throwable $e) {
                // ignore and fallback to legacy FlexForm value
            }
        }
        // Legacy fallback: old FlexForm text input (kept for backwards compatibility if present)
        if ($previewUrl === '') {
            $legacyPreview = (string)($settings['default']['preview'] ?? '');
            if ($legacyPreview !== '') {
                $previewUrl = $legacyPreview;
            }
        }
        if ($previewUrl !== '') {
            $defaults['preview'] = $previewUrl;
        }

        // Build scenes from selected Scene records
        $scenes = [];
        $sceneUidsCsv = (string)($settings['sceneRecords'] ?? '');
        $sceneUids = array_values(array_filter(array_map('intval', GeneralUtility::intExplode(',', $sceneUidsCsv, true))));
        if (!empty($sceneUids)) {
            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $qb = $connectionPool->getQueryBuilderForTable('tx_pannellum_scene');
            $qb->getRestrictions()->removeAll(); // we filter manually for hidden/deleted via enableFields is not trivial here
            $rows = $qb
                ->select('uid', 'identifier', 'title', 'type', 'panorama', 'hotspot_debug', 'hotspots', 'hidden', 'deleted', 'starttime', 'endtime')
                ->from('tx_pannellum_scene')
                ->where($qb->expr()->in('uid', $qb->createNamedParameter($sceneUids, Connection::PARAM_INT_ARRAY)))
                ->executeQuery()
                ->fetchAllAssociative();

            // preserve selection order
            $rowsByUid = [];
            foreach ($rows as $row) {
                $rowsByUid[(int)$row['uid']] = $row;
            }
            $now = time();

            // build array of available scenes first to sort out hotspots type "scene" with missing target later on
            foreach ($sceneUids as $uid) {
                if (!isset($rowsByUid[$uid])) {
                    continue;
                }
                $row = $rowsByUid[$uid];
                $identifier = trim((string)$row['identifier']);
                if ($identifier === '') {
                    continue;
                }
                $scenes[$identifier] = [];
            }

            foreach ($sceneUids as $uid) {
                if (!isset($rowsByUid[$uid])) {
                    continue;
                }
                $row = $rowsByUid[$uid];
                // basic visibility checks
                if ((int)$row['deleted'] === 1) { continue; }
                if ((int)$row['hidden'] === 1) { continue; }
                if ((int)$row['starttime'] > 0 && (int)$row['starttime'] > $now) { continue; }
                if ((int)$row['endtime'] > 0 && (int)$row['endtime'] < $now) { continue; }

                $identifier = trim((string)$row['identifier']);
                if ($identifier === '') { continue; }

                // Determine panorama URL: prefer FAL reference, fallback to legacy string field
                $panoramaUrl = '';
                try {
                    /** @var FileRepository $fileRepository */
                    $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
                    $fileRefs = $fileRepository->findByRelation('tx_pannellum_scene', 'panorama', (int)$row['uid']);
                    if (!empty($fileRefs)) {
                        $firstRef = $fileRefs[0];
                        $fileObject = $firstRef->getOriginalFile();
                        $publicUrl = $fileObject->getPublicUrl();
                        if (is_string($publicUrl) && $publicUrl !== '') {
                            $panoramaUrl = $publicUrl;
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore and use fallback
                }
                if ($panoramaUrl === '') {
                    $panoramaUrl = (string)($row['panorama'] ?? '');
                }

                $sceneArr = [
                    'title' => (string)($row['title'] ?? ''),
                    'type' => (string)($row['type'] ?? 'equirectangular'),
                    'panorama' => $panoramaUrl,
                ];
                if (!empty($row['hotspot_debug'])) {
                    $sceneArr['hotSpotDebug'] = (bool)$row['hotspot_debug'];
                }

                // Parse hotspots flexform of the scene record
                $hotspots = [];
                $hotspotsXml = (string)($row['hotspots'] ?? '');
                if ($hotspotsXml !== '') {
                    /** @var FlexFormService $flexFormService */
                    $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
                    $hotspotsArray = $flexFormService->convertFlexFormContentToArray($hotspotsXml);
                    if (!empty($hotspotsArray['settings']['hotspots']) && is_array($hotspotsArray['settings']['hotspots'])) {
                        foreach ($hotspotsArray['settings']['hotspots'] as $item) {
                            $hs = $item['hotspot'] ?? null;
                            if (!is_array($hs)) { continue; }
                            $hotspot = [];
                            if ($hs['pitch'] !== '' && $hs['pitch'] !== null) {
                                $hotspot['pitch'] = (float)$hs['pitch'];
                            }
                            if ($hs['yaw'] !== '' && $hs['yaw'] !== null) {
                                $hotspot['yaw'] = (float)$hs['yaw'];
                            }
                            $type = (string)($hs['type'] ?? 'scene');
                            if ($type !== '') { $hotspot['type'] = $type; }
                            $text = (string)($hs['text'] ?? '');
                            if ($text !== '') { $hotspot['text'] = $text; }
                            // Type-specific fields
                            if ($type === 'scene') {
                                $sceneId = (string)($hs['sceneId'] ?? '');
                                if ($sceneId !== '') { $hotspot['sceneId'] = $sceneId; }
                                if (!array_key_exists($sceneId, $scenes)) {
                                    // sort out hotspots type "scene" with missing target scene
                                    continue;
                                }

                                // targetPitch: numeric or "same"
                                $rawTargetPitch = isset($hs['targetPitch']) ? trim((string)$hs['targetPitch']) : '';
                                if ($rawTargetPitch !== '') {
                                    if (is_numeric($rawTargetPitch)) {
                                        $hotspot['targetPitch'] = (float)$rawTargetPitch;
                                    } elseif (strcasecmp($rawTargetPitch, 'same') === 0) {
                                        $hotspot['targetPitch'] = 'same';
                                    }
                                }

                                // targetYaw: numeric or "same" / "sameAzimuth"
                                $rawTargetYaw = isset($hs['targetYaw']) ? trim((string)$hs['targetYaw']) : '';
                                if ($rawTargetYaw !== '') {
                                    if (is_numeric($rawTargetYaw)) {
                                        $hotspot['targetYaw'] = (float)$rawTargetYaw;
                                    } elseif (strcasecmp($rawTargetYaw, 'same') === 0) {
                                        $hotspot['targetYaw'] = 'same';
                                    } elseif (strcasecmp($rawTargetYaw, 'sameAzimuth') === 0) {
                                        $hotspot['targetYaw'] = 'sameAzimuth';
                                    }
                                }

                                // targetHfov: numeric or "same"
                                $rawTargetHfov = isset($hs['targetHfov']) ? trim((string)$hs['targetHfov']) : '';
                                if ($rawTargetHfov !== '') {
                                    if (is_numeric($rawTargetHfov)) {
                                        $hotspot['targetHfov'] = (float)$rawTargetHfov;
                                    } elseif (strcasecmp($rawTargetHfov, 'same') === 0) {
                                        $hotspot['targetHfov'] = 'same';
                                    }
                                }
                            } elseif ($type === 'info') {
                                // URL only meaningful for info hotspots
                                $url = isset($hs['url']) ? trim((string)$hs['url']) : '';
                                if ($url !== '') {
                                    // Pannellum expects key "URL" (uppercase)
                                    $hotspot['URL'] = $url;
                                }
                                // Ensure scene-specific fields are not set for info type
                            }
                            if (!empty($hotspot)) { $hotspots[] = $hotspot; }
                        }
                    }
                }
                if (!empty($hotspots)) {
                    $sceneArr['hotSpots'] = $hotspots;
                }

                $scenes[$identifier] = $sceneArr;
            }
        }

        // Determine firstScene automatically: first identifier in the scenes array (selection order preserved)
        if (!empty($scenes)) {
            $firstIdentifier = array_key_first($scenes);
            if (is_string($firstIdentifier) && $firstIdentifier !== '') {
                $defaults['firstScene'] = $firstIdentifier;
            }
        }

        $config = [
            'default' => $defaults,
            'scenes' => $scenes,
        ];

        $configJson = json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $this->view->assignMultiple([
            'settings' => $settings,
            'config' => $config,
            'configJson' => $configJson,
            'contentElementId' => $contentElementId,
        ]);
        return $this->htmlResponse();
    }
}
