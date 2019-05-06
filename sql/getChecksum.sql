CREATE OR REPLACE FUNCTION "getChecksum" (
)
RETURNS text AS
$body$
BEGIN
return
encode(
    sha256(
        decode(
            array_to_string(array(select hash from sblink order by hash), '')
        , 'hex')
    )
, 'hex')
;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
PARALLEL UNSAFE
COST 100;

ALTER FUNCTION "getChecksum" ()
  OWNER TO lessn;