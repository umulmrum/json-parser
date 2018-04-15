<?php


namespace umulmrum\JsonParser\State;

/**
 * Holds instances of all defined JSON states, so that only a single instance of each state is required, avoiding
 * instantiating objects.
 */
class States
{
    /**
     * @var StateInterface
     */
    public static $DOCUMENT_START;
    /**
     * @var StateInterface
     */
    public static $DOCUMENT_END;
    /**
     * @var StateInterface
     */
    public static $ROOT_ARRAY;
    /**
     * @var StateInterface
     */
    public static $ROOT_OBJECT;
    /**
     * @var StateInterface
     */
    public static $OBJECT;
    /**
     * @var StateInterface
     */
    public static $ARRAY;
    /**
     * @var StateInterface
     */
    public static $VALUE;
    /**
     * @var StateInterface
     */
    public static $STRING;
    /**
     * @var StateInterface
     */
    public static $NUMERIC;
    /**
     * @var StateInterface
     */
    public static $TRUE;
    /**
     * @var StateInterface
     */
    public static $FALSE;
    /**
     * @var StateInterface
     */
    public static $NULL;

    public static function init()
    {
        self::$DOCUMENT_START = new DocumentStartState();
        self::$DOCUMENT_END = new DocumentEndState();
        self::$ROOT_OBJECT = new RootObjectState();
        self::$ROOT_ARRAY = new RootArrayState();
        self::$OBJECT = new ObjectState();
        self::$ARRAY = new ArrayState();
        self::$VALUE = new ValueState();
        self::$STRING = new StringState();
        self::$NUMERIC = new NumericState();
        self::$TRUE = new TrueState();
        self::$FALSE = new FalseState();
        self::$NULL = new NullState();
    }
}

States::init();