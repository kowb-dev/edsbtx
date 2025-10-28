<?php
// Modal window component for certificate viewing
// This file is included in certificates.php
?>

<div class="edsys-modal-overlay" id="certificateModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="edsys-modal-container">
        <!-- Modal Header -->
        <div class="edsys-modal-header">
            <div class="edsys-modal-title-section">
                <h2 class="edsys-modal-title" id="modalTitle">Просмотр сертификата</h2>
                <div class="edsys-modal-meta">
                    <span class="edsys-modal-type" id="modalType">Сертификат соответствия</span>
                    <span class="edsys-modal-valid" id="modalValid"></span>
                </div>
            </div>

            <div class="edsys-modal-controls">
                <!-- Navigation buttons -->
                <button
                        class="edsys-modal-nav-btn edsys-prev-btn"
                        id="modalPrevBtn"
                        onclick="EdsysCertificates.navigateModal('prev')"
                        aria-label="Предыдущий сертификат"
                        title="Предыдущий сертификат"
                >
                    <i class="ph ph-thin ph-caret-left"></i>
                </button>

                <button
                        class="edsys-modal-nav-btn edsys-next-btn"
                        id="modalNextBtn"
                        onclick="EdsysCertificates.navigateModal('next')"
                        aria-label="Следующий сертификат"
                        title="Следующий сертификат"
                >
                    <i class="ph ph-thin ph-caret-right"></i>
                </button>

                <!-- Zoom controls -->
                <button
                        class="edsys-modal-zoom-btn"
                        id="modalZoomOut"
                        onclick="EdsysCertificates.zoomModal('out')"
                        aria-label="Уменьшить"
                        title="Уменьшить"
                >
                    <i class="ph ph-thin ph-minus"></i>
                </button>

                <span class="edsys-zoom-level" id="zoomLevel">100%</span>

                <button
                        class="edsys-modal-zoom-btn"
                        id="modalZoomIn"
                        onclick="EdsysCertificates.zoomModal('in')"
                        aria-label="Увеличить"
                        title="Увеличить"
                >
                    <i class="ph ph-thin ph-plus"></i>
                </button>

                <button
                        class="edsys-modal-zoom-btn"
                        id="modalZoomReset"
                        onclick="EdsysCertificates.zoomModal('reset')"
                        aria-label="Сбросить масштаб"
                        title="Сбросить масштаб"
                >
                    <i class="ph ph-thin ph-arrows-out"></i>
                </button>

                <!-- Close button -->
                <button
                        class="edsys-modal-close-btn"
                        onclick="EdsysCertificates.closeModal()"
                        aria-label="Закрыть"
                        title="Закрыть"
                >
                    <i class="ph ph-thin ph-x"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="edsys-modal-body">
            <div class="edsys-modal-image-container" id="modalImageContainer">
                <div class="edsys-modal-image-wrapper" id="modalImageWrapper">
                    <img
                            class="edsys-modal-image"
                            id="modalImage"
                            src=""
                            alt=""
                            draggable="false"
                    />
                </div>

                <!-- Loading spinner -->
                <div class="edsys-modal-loading" id="modalLoading">
                    <div class="edsys-spinner"></div>
                    <span>Загрузка...</span>
                </div>

                <!-- Zoom hint -->
                <div class="edsys-zoom-hint" id="zoomHint">
                    <i class="ph ph-thin ph-magnifying-glass-plus"></i>
                    <span>Кликните для увеличения</span>
                </div>
            </div>

            <!-- Certificate info sidebar -->
            <div class="edsys-modal-info">
                <div class="edsys-modal-description">
                    <h3>Описание документа</h3>
                    <p id="modalDescription">Описание сертификата будет отображено здесь</p>
                </div>

                <div class="edsys-modal-details">
                    <h3>Детали</h3>
                    <div class="edsys-detail-item">
                        <span class="edsys-detail-label">Тип документа:</span>
                        <span class="edsys-detail-value" id="modalTypeDetail">Сертификат соответствия</span>
                    </div>

                    <div class="edsys-detail-item" id="modalValidDetail">
                        <span class="edsys-detail-label">Действителен до:</span>
                        <span class="edsys-detail-value" id="modalValidValue">—</span>
                    </div>

                    <div class="edsys-detail-item">
                        <span class="edsys-detail-label">Статус:</span>
                        <span class="edsys-detail-value edsys-status-active">
                            <i class="ph ph-thin ph-check-circle"></i>
                            Действующий
                        </span>
                    </div>
                </div>

                <!-- Additional actions -->
                <div class="edsys-modal-actions">
                    <button class="edsys-action-btn edsys-btn-secondary" onclick="EdsysCertificates.printCertificate()">
                        <i class="ph ph-thin ph-printer"></i>
                        Печать
                    </button>

                    <button class="edsys-action-btn edsys-btn-secondary" onclick="EdsysCertificates.shareCertificate()">
                        <i class="ph ph-thin ph-share-network"></i>
                        Поделиться
                    </button>
                </div>

                <!-- Certificate counter -->
                <div class="edsys-modal-counter">
                    <span id="modalCounter">1 из 5</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="edsys-modal-footer">
            <div class="edsys-modal-navigation">
                <button
                        class="edsys-nav-button edsys-btn-outline"
                        id="modalPrevBtnFooter"
                        onclick="EdsysCertificates.navigateModal('prev')"
                >
                    <i class="ph ph-thin ph-caret-left"></i>
                    Предыдущий
                </button>

                <button
                        class="edsys-nav-button edsys-btn-outline"
                        id="modalNextBtnFooter"
                        onclick="EdsysCertificates.navigateModal('next')"
                >
                    Следующий
                    <i class="ph ph-thin ph-caret-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Click outside to close -->
    <div class="edsys-modal-backdrop" onclick="EdsysCertificates.closeModal()"></div>
</div>