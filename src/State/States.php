<?php

namespace umulmrum\JsonParser\State;

/**
 * Holds instances of all defined JSON states, so that only a single instance of each state is required, avoiding
 * instantiating objects.
 *
 * @internal
 */
class States
{
    /**
     * @var DocumentStartState
     */
    public static $DOCUMENT_START;
    /**
     * @var DocumentEndState
     */
    public static $DOCUMENT_END;
    /**
     * @var RootArrayState
     */
    public static $ROOT_ARRAY;
    /**
     * @var RootObjectState
     */
    public static $ROOT_OBJECT;
    /**
     * @var ObjectState
     */
    public static $OBJECT;
    /**
     * @var ArrayState
     */
    public static $ARRAY;
    /**
     * @var ValueState
     */
    public static $VALUE;
    /**
     * @var StringState
     */
    public static $STRING;
    /**
     * @var EscapedStringState
     */
    public static $ESCAPED_STRING;
    /**
     * @var NumericState
     */
    public static $NUMERIC;
    /**
     * @var TrueState
     */
    public static $TRUE;
    /**
     * @var FalseState
     */
    public static $FALSE;
    /**
     * @var NullState
     */
    public static $NULL;

    public static function init(): void
    {
        self::$DOCUMENT_START = new DocumentStartState();
        self::$DOCUMENT_END = new DocumentEndState();
        self::$ROOT_OBJECT = new RootObjectState();
        self::$ROOT_ARRAY = new RootArrayState();
        self::$OBJECT = new ObjectState();
        self::$ARRAY = new ArrayState();
        self::$VALUE = new ValueState();
        self::$STRING = new StringState();
        self::$ESCAPED_STRING = new EscapedStringState();
        self::$NUMERIC = new NumericState();
        self::$TRUE = new TrueState();
        self::$FALSE = new FalseState();
        self::$NULL = new NullState();
    }
}

States::init();
