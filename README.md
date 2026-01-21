# One Page Checkout for PrestaShop 1.7.6

Módulo de checkout personalizado para PrestaShop 1.7.6 con diseño moderno y funcionalidades avanzadas.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PrestaShop](https://img.shields.io/badge/PrestaShop-1.7.6--1.7.9-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## 🎯 Características Principales

### ✨ Diseño Moderno
- **Interfaz limpia y profesional** con diseño One Page
- **Fuente Quicksand de Google Fonts** para una tipografía elegante
- **Iconos Material Symbols** para una apariencia moderna
- **Diseño responsive** optimizado para mobile, tablet y desktop
- **Header sticky** con logo y badge de seguridad
- **Resumen lateral sticky** con todos los detalles del pedido

### 🚀 Sin Recargas de Página (AJAX)
- Toda la navegación es mediante AJAX
- Actualización dinámica de transportistas al cambiar dirección
- Actualización automática de totales al cambiar carrier
- Validación en tiempo real de cada paso
- Experiencia de usuario fluida y rápida

### 🤖 Auto-Selección Inteligente

Al cargar el checkout, el módulo automáticamente:

1. **Auto-selecciona la dirección de entrega principal** del cliente
2. **Carga los transportistas disponibles** para esa dirección
3. **Auto-selecciona el transportista más barato** (excluyendo los gratuitos)
4. **Hace scroll automático** a la sección de pago
5. **Expande la sección de pago** y colapsa las anteriores
6. **Marca las secciones completadas** con indicadores visuales

### 🎨 Componentes UI

- **Secciones colapsables/expandibles** con animaciones suaves
- **Radio buttons personalizados** con estilo moderno
- **Navegación con dots** que indica el progreso
- **Badges de seguridad** (Pago Seguro, Entrega Rápida)
- **Campo de código promocional** integrado
- **Lista de productos** con imágenes y cantidades

### 🔌 Integración Completa con PrestaShop

- Usa clases nativas: `Cart`, `Customer`, `Address`, `Carrier`, `PaymentModule`, `Order`
- Compatible con todos los módulos de pago existentes
- Compatible con todos los módulos de envío existentes
- Hooks registrados: `displayHeader`, `actionFrontControllerSetMedia`
- Override de `OrderController` para usar template personalizado

## 📋 Requisitos del Sistema

- **PrestaShop**: 1.7.6 a 1.7.9
- **PHP**: 7.1 o superior
- **MySQL**: 5.6 o superior
- **jQuery**: Incluido en PrestaShop (no requiere instalación adicional)

## 📦 Instalación

### Método 1: Instalación Manual

1. **Descargar el módulo**
   ```bash
   git clone https://github.com/muraxbl/onepagecheckout.git
   ```

2. **Subir a PrestaShop**
   - Copia la carpeta `onepagecheckout` a `/modules/` en tu instalación de PrestaShop
   - La ruta completa debería ser: `/modules/onepagecheckout/`

3. **Instalar el módulo**
   - Ve al BackOffice de PrestaShop
   - Navega a `Módulos > Module Manager`
   - Busca "One Page Checkout"
   - Haz clic en "Instalar"

4. **Limpiar caché**
   ```bash
   php bin/console cache:clear
   ```
   O desde el BackOffice: `Parámetros Avanzados > Rendimiento > Limpiar caché`

### Método 2: Instalación desde ZIP

1. Descarga el archivo ZIP del módulo
2. En el BackOffice: `Módulos > Module Manager`
3. Haz clic en "Subir un módulo"
4. Selecciona el archivo ZIP
5. El módulo se instalará automáticamente

## 🛠️ Estructura de Archivos

```
onepagecheckout/
├── onepagecheckout.php          # Clase principal del módulo
├── config.xml                    # Configuración del módulo
├── logo.png                      # Logo 128x128px
├── index.php                     # Seguridad
├── ajax.php                      # Endpoints AJAX
├── README.md                     # Documentación
├── views/
│   ├── templates/
│   │   ├── front/
│   │   │   ├── checkout.tpl     # Template Smarty del checkout
│   │   │   └── index.php
│   │   └── hook/
│   │       ├── header.tpl       # Hook para header
│   │       └── index.php
│   ├── css/
│   │   ├── checkout.css         # Estilos completos
│   │   └── index.php
│   ├── js/
│   │   ├── checkout.js          # Lógica AJAX y auto-selecciones
│   │   └── index.php
│   └── index.php
├── override/
│   └── controllers/
│       └── front/
│           ├── OrderController.php  # Override del controlador
│           └── index.php
└── translations/
    └── es.php                   # Traducciones españolas
```

## 🎯 Funcionamiento

### Al Cargar el Checkout (/order)

1. ✅ Se muestra el diseño personalizado (no el checkout nativo)
2. ✅ Si el cliente está logueado, se muestra su información
3. ✅ Se auto-selecciona su dirección de entrega principal
4. ✅ Se cargan los transportistas disponibles para esa dirección
5. ✅ Se auto-selecciona el transportista más barato (excluyendo gratuitos)
6. ✅ Se actualizan los totales con el coste de envío
7. ✅ Se hace scroll automático a la sección "Método de pago"
8. ✅ Se expande automáticamente esa sección
9. ✅ Se marcan las secciones anteriores como completadas

### Interacciones del Usuario

- **Click en sección** → Expandir/colapsar contenido
- **Cambiar dirección** → Recargar transportistas → Actualizar totales
- **Cambiar transportista** → Actualizar totales
- **Seleccionar método de pago** → Preparar para finalizar
- **Aplicar código promocional** → Actualizar descuentos y totales
- **Click en "Realizar Pedido"** → Validar → Procesar pedido

## 🔧 Configuración

El módulo no requiere configuración adicional. Funciona automáticamente una vez instalado.

Para acceder a la información del módulo:
1. Ve a `Módulos > Module Manager`
2. Busca "One Page Checkout"
3. Haz clic en "Configurar"

## 🔒 Seguridad

El módulo implementa las siguientes medidas de seguridad:

- ✅ Uso de `Tools::getValue()` para todos los inputs
- ✅ Validación de datos en servidor
- ✅ Uso de tokens CSRF en formularios
- ✅ Sanitización de outputs
- ✅ Prevención de SQL Injection
- ✅ Prevención de XSS
- ✅ Verificación de permisos de usuario
- ✅ Archivos index.php en todas las carpetas

## 🌐 Compatibilidad

### PrestaShop
- ✅ PrestaShop 1.7.6
- ✅ PrestaShop 1.7.7
- ✅ PrestaShop 1.7.8
- ✅ PrestaShop 1.7.9

### Módulos de Pago
Compatible con todos los módulos de pago estándar de PrestaShop:
- PayPal
- Stripe
- Redsys
- Transferencia bancaria
- Pago contra reembolso
- Y más...

### Módulos de Envío
Compatible con todos los módulos de envío:
- Carrier por defecto de PrestaShop
- Módulos de terceros
- Envío gratuito
- Envío con coste fijo/variable

### Navegadores
- Chrome (últimas 2 versiones)
- Firefox (últimas 2 versiones)
- Safari (últimas 2 versiones)
- Edge (últimas 2 versiones)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🐛 Troubleshooting

### El módulo no aparece en la lista de módulos

**Solución:**
1. Verifica que la carpeta esté en `/modules/onepagecheckout/`
2. Verifica los permisos de archivos (755 para carpetas, 644 para archivos)
3. Limpia la caché de PrestaShop

### El checkout sigue mostrando el diseño nativo

**Solución:**
1. Verifica que el módulo esté instalado y activado
2. Limpia la caché de PrestaShop
3. Limpia la caché del navegador
4. Verifica que el override se haya copiado correctamente en `/override/controllers/front/OrderController.php`

### Los carriers no se cargan automáticamente

**Solución:**
1. Verifica que el cliente tenga una dirección de entrega
2. Verifica que haya carriers configurados en PrestaShop
3. Verifica que los carriers estén activos y asociados a zonas
4. Abre la consola del navegador para ver errores de JavaScript

### Error 500 al acceder al checkout

**Solución:**
1. Verifica los logs de PHP en `/var/logs/`
2. Verifica los logs de PrestaShop en `/var/logs/` o `/logs/`
3. Verifica que todos los archivos del módulo estén presentes
4. Verifica la sintaxis PHP de los archivos

### El scroll automático no funciona

**Solución:**
1. Verifica que jQuery esté cargado correctamente
2. Abre la consola del navegador para ver errores
3. Verifica que el archivo `checkout.js` se cargue correctamente

## 📝 Changelog

### v1.0.0 (2024)
- ✅ Lanzamiento inicial
- ✅ Diseño moderno con Quicksand y Material Icons
- ✅ Sistema de auto-selección inteligente
- ✅ AJAX completo sin recargas
- ✅ Responsive design mobile-first
- ✅ Compatible con PrestaShop 1.7.6+

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**muraxbl**

## 📧 Soporte

Si tienes problemas o preguntas:
1. Revisa la sección de Troubleshooting
2. Abre un issue en GitHub
3. Contacta al autor

## 🙏 Agradecimientos

- PrestaShop por su excelente plataforma de e-commerce
- Google Fonts por la fuente Quicksand
- Material Design por los iconos

---

**¡Gracias por usar One Page Checkout!** 🚀
