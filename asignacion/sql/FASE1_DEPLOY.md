# Fase 1 de seguridad — Despliegue y rollback

Hasheo de las claves de `usuarios`, de forma gradual y transparente: nadie tiene
que resetear nada. Cada usuario entra con su clave de siempre y el login se la
re-hashea al vuelo, en su próximo ingreso.

Merge: `691d87f`. Punto único de revert.

---

## Lo que hay que entender antes de tocar nada

**`git revert` NO alcanza para volver atrás.** Desde que esto se despliega, cada
usuario que entra queda re-hasheado en la base. El código viejo compara
`WHERE clave = '<texto plano>'`, que jamás va a matchear un hash bcrypt. Si
revertís sólo el código, **todo el que se logueó queda afuera para siempre** — y
el texto plano ya no existe en ningún lado para recuperarlo.

Por eso el paso 1 es el backup. **Sin backup, esta fase es de ida.**

---

## Despliegue

### 1. Repo privado

Antes de pushear. Los commits describen vulnerabilidades que en prod siguen
abiertas: publicarlas con el sistema sin parchear es darle el mapa al atacante.
Settings → General → Danger Zone → Change visibility.

(Lo pedía la Fase 0 igual: `config/config_mysql*.php` se bajan hoy por
raw.githubusercontent con credenciales de prod vivas.)

### 2. Backup de las claves — EN PROD, ANTES DEL CÓDIGO

```
mysql -u<user> -p asignacion < asignacion/sql/fase1_00_backup_claves.sql
```

El script imprime tres cosas. Verificá las tres:

- `usuarios` == `respaldados` → se respaldó todo.
- `hasheadas` == 0 → todavía no se desplegó nada. Si da > 0, **parar**: alguien
  ya desplegó y el backup no sirve como línea de base.
- `COLUMN_TYPE` de `clave` == `varchar(255)`, o al menos algo **>= 60**.
  Un hash bcrypt mide 60. Si fuera más angosta, MySQL lo truncaría **en
  silencio** (prod no usa `sql_mode` estricto) y el usuario entraría una vez y
  no podría entrar nunca más. Si da < 60: **parar** y agregar un
  `MODIFY COLUMN clave VARCHAR(255)` antes de seguir.

### 3. Migración — todavía antes del código

```
mysql -u<user> -p asignacion < asignacion/sql/fase1_seguridad_debe_cambiar_clave.sql
```

Agrega `usuarios.debe_cambiar_clave TINYINT(1) NOT NULL DEFAULT 0`. Con default
0 no fuerza a nadie: es segura de correr aunque el código todavía no esté.

> Si por error subís el código antes: el login **no se rompe** (entra y
> re-hashea igual), pero tira un `Notice: Undefined index: debe_cambiar_clave`
> en cada ingreso, visible para el usuario si `display_errors` está On. Corregís
> corriendo la migración y listo.

### 4. Código

```
git push origin master        # con el repo ya privado
# y en el VPS:
git pull
```

El pull borra solo `ventas/index.php`, `ventas/web/validar.php` y los backups
muertos. Si el VPS tiene cambios locales sin commitear, el pull va a conflictuar:
resolvé eso antes.

### 5. Verificar

- Entrá con tu propia cuenta. Tiene que entrar normal, con tu clave de siempre.
- `SELECT SUM(clave LIKE '$2y$%') FROM usuarios;` → tiene que ir subiendo a
  medida que la gente entra. Ese número es el progreso de la migración.
- Que nadie reporte que no puede entrar.

Dejalo correr unos días. La columna se hashea sola.

---

## Rollout del cambio forzado (aparte, cuando quieras)

Recién cuando la fase esté estable. Fuerza a elegir clave nueva con política
(mín. 8, mayúscula, minúscula, símbolo) en el próximo ingreso:

```sql
UPDATE usuarios SET debe_cambiar_clave = 1 WHERE activo = 1;
```

Conviene arrancar por un grupo chico (o por vos) antes que por los 109 activos:

```sql
UPDATE usuarios SET debe_cambiar_clave = 1 WHERE idusuario IN (...);
```

---

## Rollback

**Orden: primero el código, después el SQL.** Al revés queda una ventana en la
que el código nuevo re-hashea de nuevo lo que el script acaba de restaurar.

```
git revert -m 1 691d87f
git push origin master
# en el VPS:
git pull

# recién ahora:
mysql -u<user> -p asignacion < asignacion/sql/fase1_99_rollback.sql
```

El script verifica solo: `sin_restaurar` tiene que dar **0**. Además lista los
usuarios creados **después** del backup (altas hechas durante la ventana): esos
no están respaldados, quedan hasheados y el código viejo no los va a poder
validar. Hay que resetearles la clave a mano desde el admin.

Efecto secundario: si alguien cambió su clave por `login/cambiar_clave.php`
durante la ventana, el rollback lo devuelve a su clave **anterior**. Entra
igual, pero con la vieja.

La columna `debe_cambiar_clave` se puede dejar: el código viejo la ignora.

---

## Después, cuando esté confirmado

```sql
DROP TABLE usuarios_backup_prefase1;
```

**No te olvides de este paso.** Mientras exista, esa tabla tiene las claves de
todos en texto plano: es tan sensible como la tabla original antes de la fase.
