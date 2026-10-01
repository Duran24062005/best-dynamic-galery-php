El error `Bind for 0.0.0.0:5433 failed: port is already allocated` significa que **el puerto 5433 de tu máquina local ya está siendo utilizado** por otro servicio o por otro contenedor de Docker.

---

### Diagnóstico y Solución

#### Paso 1: Ver qué proceso o contenedor usa el puerto 5433

Para verificar qué está ocupando ese puerto en tu sistema, ejecuta:

```bash
sudo lsof -i :5433

```

o también:

```bash
sudo ss -tulpn | grep 5433

```

Si es otro contenedor de Docker, puedes listarlo con:

```bash
docker ps

```

---

#### Opción A: Mapear un puerto host diferente (Recomendado)

La forma más rápida de solucionarlo sin detener otros servicios es cambiar el **puerto del host** en tu archivo `docker-compose.yml`.

Abre tu `docker-compose.yml` y busca la sección del servicio `database`:

```yaml
services:
  database:
    # ...
    ports:
      - "5434:5432"  # Cambia el puerto de la izquierda (host) a 5434 u otro libre

```

> **Nota:** La estructura es `"PUERTO_HOST:PUERTO_CONTENEDOR"`. Solo cambia el número a la izquierda de los dos puntos. El puerto de la derecha (`5432`) se mantiene igual porque es el puerto interno del contenedor.

---

#### Opción B: Liberar el puerto 5433

Si el puerto está ocupado por un contenedor viejo o detenido que no recordabas:

1. Detén y elimina contenedores huérfanos o en ejecución:
```bash
docker stop $(docker ps -q)

```


2. Si es un proceso local de PostgreSQL u otro servicio en el host, finalízalo con su PID:
```bash
sudo kill -9 <PID>

```



---

### Corrigiendo el comando `docker compose ps`

En la última línea de tu terminal el comando falló por la sintaxis. El flag correcto para ver logs o el estado de los contenedores es:

* **Ver estado de los contenedores:**
```bash
docker compose ps

```


* **Ver los logs de los contenedores:**
```bash
docker compose logs -f

```