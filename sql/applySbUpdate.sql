CREATE OR REPLACE FUNCTION "applySbUpdate" (
    additions text [],
    deletions integer []
)
    RETURNS text AS
$body$
declare
    hashtoadd varchar;
BEGIN

    -- Deletions

    delete
    from
        sblink
    where
            sblink.hash in
            (
                select
                    hash
                from
                    (
                        select
                                        ROW_NUMBER () OVER (order by sblink.hash) - 1 as "rownum",
                                        sblink.hash
                        from
                            sblink
                    )
                        as numhash
                where
                        rownum = any(deletions)
            )
    ;

-- Additions

    FOREACH hashtoadd in array additions
        loop
            insert into sblink (hash) values (hashtoadd);
        end loop;

-- Hash computing

    return "getChecksum"();

END;
$body$
    LANGUAGE 'plpgsql'
    VOLATILE
    CALLED ON NULL INPUT
    SECURITY INVOKER
    PARALLEL UNSAFE
    COST 100;

ALTER FUNCTION "applySbUpdate" (additions text [], deletions integer [])
    OWNER TO lessn;