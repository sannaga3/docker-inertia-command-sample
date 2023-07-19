export default function PrimaryButton({
    className = "",
    disabled,
    children,
    ...props
}) {
    return (
        <button
            {...props}
            className={
                `inline-flex items-center border border-transparent rounded-md font-semibold text-xs tracking-wides outline-none bg-gray-800 text-white px-2 py-1 mb-2 hover:bg-gray-700 ${
                    disabled && "opacity-25"
                } ` + className
            }
            disabled={disabled}
        >
            {children}
        </button>
    );
}
