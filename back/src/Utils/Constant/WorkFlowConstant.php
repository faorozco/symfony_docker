<?php

namespace App\Utils\Constant;

class WorkFlowConstant {
    const STEP_ACTIVE = "ACTIVO";
    const STEP_COMPLETED = "COMPLETADO";
    const STEP_EXPIRED = "VENCIDO";
    const STEP_CANCELLED = "CANCELADO"; // cancelar un paso es cancelar tambien el flujo
    const STEP_RETURNED = "DEVUELTO"; // Se debe devolver a cualquier paso anterior, hacer explicito si se debe continuar al paso desde el que se devolvió o se deba continuar al paso siguiente
    const STEP_APPROVAL = "PENDIENTE_VISTO_BUENO"; // Pendiente de visto bueno para aprobación
    const STEP_DISRUPTED = "INTERRUMPIDO"; // Solo se INTERRUMPE por un evento.

    const FLOW_ACTIVE = "ACTIVO";
    const FLOW_COMPLETED = "COMPLETADO"; // Agregar breve descripción 
    const FLOW_CANCELLED = "CANCELADO"; // Solo se cancela manualmente o porque se cancele un paso.
    const FLOW_DISRUPTED = "INTERRUMPIDO"; // Solo se INTERRUMPE por un evento.
}