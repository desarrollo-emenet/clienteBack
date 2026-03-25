<?php

return [
    'mode_privileged' => "enable\n",
    'config_term' => "config\n",
    "interface" => "interface gpon :frame/:numTarjeta\n",

    "agrega_ont" => "ont add :puertoTarjeta :ont_id sn-auth :serie_ont omci ont-lineprofile-id 100 ont-srvprofile-id 100 desc :clienteFormato\n\n",
    "agrega_serviceport" => "service-port :service_port vlan :vlan gpon :frame/:numTarjeta/:puertoTarjeta ont :ont_id gemport :gemport multi-service user-vlan :vlan tag-transform translate rx-cttr :pcarga tx-cttr :pdescarga\n",
    "elimina_ont" => "ont delete :puertoTarjeta :ont_id\n",
    "elimina_serviceport" => "undo service-port :service_port\n",
    "guardar_cambios" => "save\n",
    "reiniciar_ont" => "ont reset :puertoTarjeta :ont_id\n",

    "optical_info" => "display ont optical-info :puertoTarjeta :ont_id\n\n",
    "ont_info" => "display ont info by-sn :serie_ont\n",
    "ont_mac" => "display mac-address service-port :service_port\n\n",

    'regresar' => "quit\n",
    'confirmar' => 'y',
];
