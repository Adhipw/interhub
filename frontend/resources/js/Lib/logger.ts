type LogLevel = 'debug' | 'error' | 'log' | 'warn';

const write = (level: LogLevel, args: unknown[]) => {
    if (!import.meta.env.DEV) return;

    globalThis['console']?.[level]?.(...args);
};

const logger = {
    debug: (...args: unknown[]) => write('debug', args),
    error: (...args: unknown[]) => write('error', args),
    log: (...args: unknown[]) => write('log', args),
    warn: (...args: unknown[]) => write('warn', args),
};

export default logger;
